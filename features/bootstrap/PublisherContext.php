<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Symfony2Extension\Driver\KernelDriver;
use GuzzleHttp\Client;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Mock;
use MessageContext\Domain\Message;
use MessageContext\Domain\Publisher;
use MessageContext\Domain\Repository\MessageRepositoryInterface;
use MessageContext\Domain\ValueObjects\BodyMessage;
use MessageContext\Domain\ValueObjects\Channel;
use MessageContext\Domain\ValueObjects\ChannelAuthorization;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\MessageId;
use MessageContext\Domain\ValueObjects\PublisherId;
use MessageContext\InfrastructureBundle\Repository\InMemory\PublisherRepository;
use MessageContext\InfrastructureBundle\Tests\Resources\MockResponsesLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;

class PublisherContext extends KernelDriver implements Context, SnippetAcceptingContext
{
    private $publisherId;
    private $channelId;
    private $message;
    private $messageId;
    private $isClosed;

    /** @var  Mock */
    private $mock;

    /**
     * @BeforeScenario
     */
    public function cleanMockServices(BeforeScenarioScope $scope)
    {
        $this->mock = new Mock();
    }

    /**
     * @Given a publisher with id :publisherId
     */
    public function aPublisherWithId($publisherId)
    {
        $this->publisherId = $publisherId;
    }

    /**
     * @Given exists a :state channel with id :channelId
     */
    public function existsAChannelWithId($channelId, $state)
    {
        $isClosed = $state === "open" ? "false" : "true";
        $this->isClosed = $isClosed;

        $this->channelId = $channelId;
        $channelResponseTemplate = MockResponsesLocator::getResponseTemplate("channel200response.json");
        $body = sprintf($channelResponseTemplate, $this->channelId, $this->isClosed);

        $response = new \GuzzleHttp\Message\Response(200, [], Stream::factory($body));

        $response->addHeader("Content-Type", "application/json");
        $this->mock->addResponse(
            $response
        );
    }

    /**
     * @Given the publisher is authorized to publish message on that channel
     */
    public function thePublisherIsAuthorizedToPublishMessageOnTheChannel()
    {
        $response = new \GuzzleHttp\Message\Response(200,[], Stream::factory(
            sprintf("{\"publisher_id\" : \"%s\", \"channel_id\": %s, \"authorized\": %s}",
                $this->publisherId,
                $this->channelId,
                "true")
        ));
        $response->addHeader("Content-Type", "application/json");
        $this->mock->addResponse($response);
    }

    /**
     * @When the publisher write the message :message
     */
    public function thePublisherWriteTheMessage($message)
    {
        if($this->mock->count() > 0) {
            $container = $this->getClient()->getContainer();

            /** @var Client $httpClient */
            $httpClient = $container->get('message_context.infrastructure.guzzle_http_client');
            $httpClient->getEmitter()->attach($this->mock);
        }

        $this->message = $message;
        $bodyRequest = ['publisher_id' => $this->publisherId, 'channel_id' => $this->channelId, "message" => $this->message];

        $this->getClient()->request(
            "POST",
            $this->prepareUrl(sprintf("/api/messages")),
            array(),
            array(),
            array(["Accept", "application/json"]),
            json_encode($bodyRequest)
        );
    }

    /**
     * @Then a new message will be created on the channel
     */
    public function aNewMessageWillBeCreatedOnTheChannel()
    {
        $content = json_decode($this->getContent(), true);

        PHPUnit_Framework_Assert::assertEquals($this->message, $content["message"]);
        PHPUnit_Framework_Assert::assertEquals($this->publisherId, $content["publisher_id"]);
        PHPUnit_Framework_Assert::assertEquals($this->channelId, $content["channel_id"]);
        PHPUnit_Framework_Assert::assertEquals(Response::HTTP_CREATED, $this->getResponse()->getStatus());
    }

    /**
     * @Given the publisher is not authorized to publish message on that channel
     */
    public function thePublisherIsNotAuthorizedToPublishMessageOnThatChannel()
    {
        $this->mock->addResponse(
            new \GuzzleHttp\Message\Response(200, ["Content-Type" => "application/json"], Stream::factory(
                sprintf("{\"publisher_id\" : \"%s\", \"channel_id\": %s, \"authorized\": %s}",
                    $this->publisherId,
                    $this->channelId,
                    "false")
            )));
    }

    /**
     * @Then the publisher is informed that is not authorized
     */
    public function thePublisherIsInformedThatIsNotAuthorized()
    {
        $response = $this->getResponse();
        PHPUnit_Framework_Assert::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * @Then no new messages will be added on that channel
     */
    public function noNewMessagesWillBeAddedOnThatChannel()
    {
        /** @var Container $container */
        $container = $this->getClient()->getContainer();

        /** @var MessageRepositoryInterface $messageRepository */
        $messageRepository = $container->get("message_context.infrastructure.message_repository");

        $messages = $messageRepository->getAll();

        foreach($messages as $message) {
            PHPUnit_Framework_Assert::assertNotEquals($this->message, $message->getMessage());
        }
    }

    /**
     * @Given a channel with id :arg1 doesn't exists
     */
    public function aChannelWithIdDoesNotExists($channelId)
    {
        $this->channelId = $channelId;

        $this->mock->addResponse(
            new \GuzzleHttp\Message\Response(404, [])
        );
    }

    /**
     * @Then the publisher is informed that the channel doesn't exists
     */
    public function thePublisherIsInformedThatTheChannelDoesNotExists()
    {
        $response = $this->getResponse();
        PHPUnit_Framework_Assert::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatus());
    }

    /**
     * @Given exists message with id :messageId on the channel :channelId
     */
    public function aMessageWithIdOnTheChannel($messageId, $channelId)
    {
        $this->messageId = $messageId;
        $this->channelId = $channelId;
    }

    /**
     * @Given the publisher is the owner of the message
     */
    public function thePublisherIsTheOwnerOfTheMessage()
    {
        $publisherId = new PublisherId($this->publisherId);
        $channelId = new ChannelId($this->channelId);
        $publisher = new Publisher($publisherId);

        $message = $publisher->publishOnChannel(
            new Channel($channelId, false),
            new ChannelAuthorization($publisherId, $channelId, true),
            new BodyMessage("hello")
        );

        $reflectionClass = new ReflectionClass(Message::class);
        $reflectionProperty = $reflectionClass->getProperty('messageId');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($message, new MessageId($this->messageId));

        /** @var Container $container */
        $container = $this->getClient()->getContainer();

        /** @var MessageRepositoryInterface $messageRepository */
        $messageRepository = $container->get("message_context.infrastructure.message_repository");
        $messageRepository->add($message);

        /** @var PublisherRepository $messageRepository */
        $publisherRepository = $container->get("message_context.infrastructure.publisher_repository");
        $publisherRepository->add($publisher);
    }

    /**
     * @When the publisher delete the message
     */
    public function thePublisherDeleteTheMessage()
    {
        if($this->mock->count() > 0) {
            $container = $this->getClient()->getContainer();

            /** @var Client $httpClient */
            $httpClient = $container->get('message_context.infrastructure.guzzle_http_client');
            $httpClient->getEmitter()->attach($this->mock);
        }

        $this->getClient()->request(
            "DELETE",
            $this->prepareUrl(sprintf("/api/messages/%s", $this->messageId)),
            array(),
            array(),
            array(["Accept", "application/json"])
        );
    }

    /**
     * @Then the message will be deleted from the channel
     */
    public function theMessageWillBeDeletedFromTheChannel()
    {
        $container = $this->getClient()->getContainer();

        $response = $this->getResponse();
        PHPUnit_Framework_Assert::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatus());

        /** @var MessageRepositoryInterface $messageRepository */
        $messageRepository = $container->get("message_context.infrastructure.message_repository");

        $collection = $messageRepository->get(new MessageId($this->messageId));
        PHPUnit_Framework_Assert::assertCount(0, $collection);
    }

    /**
     * @Given the publisher is not the owner of the message
     */
    public function thePublisherIsNotTheOwnerOfTheMessage()
    {
        $message = new Message(new PublisherId("7777"), new ChannelId($this->channelId), new BodyMessage("hello everyone"));

        $reflectionClass = new ReflectionClass(Message::class);
        $reflectionProperty = $reflectionClass->getProperty('messageId');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($message, new MessageId($this->messageId));

        //create the message with message repository
        /** @var Container $container */
        $container = $this->getClient()->getContainer();

        /** @var MessageRepositoryInterface $messageRepository */
        $messageRepository = $container->get("message_context.infrastructure.message_repository");
        $messageRepository->add($message);
    }

    /**
     * @Then the publisher is informed that is not the owner of that message
     */
    public function theUserIsInformedThatIsNotTheOwnerOfThatMessage()
    {
        $response = $this->getResponse();
        PHPUnit_Framework_Assert::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * @Then the message will not be deleted from the channel
     */
    public function theMessageWillNotBeDeletedFromTheChannel()
    {
        $container = $this->getClient()->getContainer();

        /** @var MessageRepositoryInterface $messageRepository */
        $messageRepository = $container->get("message_context.infrastructure.message_repository");

        $collection = $messageRepository->get(new MessageId($this->messageId));
        PHPUnit_Framework_Assert::assertCount(1, $collection);
    }

    /**
     * @Then the publisher is informed that is not possible to perform action on a closed channel
     */
    public function thePublisherIsInformedThatIsNotPossiblePerformActionsOnAClosedChannel()
    {
        $response = $this->getResponse();
        PHPUnit_Framework_Assert::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatus());
    }

}