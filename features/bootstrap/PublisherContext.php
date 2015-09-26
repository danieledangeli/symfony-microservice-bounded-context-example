<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Symfony2Extension\Driver\KernelDriver;
use GuzzleHttp\Client;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Mock;
use PostContext\Domain\Channel;
use PostContext\Domain\Message;
use PostContext\Domain\Publisher;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\BodyMessage;
use PostContext\Domain\ValueObjects\MessageId;
use PostContext\Domain\ValueObjects\PublisherId;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;

class PublisherContext extends KernelDriver implements Context, SnippetAcceptingContext
{
    private $publisherId;
    private $channelId;
    private $message;
    private $messageId;

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
     * @Given exists a channel with id :channelId
     */
    public function existsAChannelWithId($channelId)
    {
        $this->channelId = $channelId;

        $response = new \GuzzleHttp\Message\Response(200, [], Stream::factory(sprintf("{\"id\" : \"%s\"}", $this->channelId)));
        $response->addHeader("Content-Type", "application/json");
        $this->mock->addResponse(
            $response
        );
    }

    /**
     * @Given the publisher is authorized to publish message on the channel
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
            $httpClient = $container->get('post_context.infrastructure.guzzle_http_client');
            $httpClient->getEmitter()->attach($this->mock);
        }

        $this->message = $message;
        $bodyRequest = ['publisher_id' => $this->publisherId, 'channel_id' => $this->channelId, "message" => $this->message];

        $this->getClient()->request(
            "POST",
            $this->prepareUrl(sprintf("/api/posts")),
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
     * @Given the publisher is not authorized to publish message on the channel
     */
    public function thePublisherIsNotAuthorizedToPublishMessageOnTheChannel()
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

        /** @var \PostContext\Domain\Repository\PostRepositoryInterface $postRepository */
        $postRepository = $container->get("post_context.infrastructure.post_repository");

        $posts = $postRepository->getAll();

        foreach($posts as $post) {
            PHPUnit_Framework_Assert::assertNotEquals($this->message, $post->getMessage());
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
     * @Given a message with id :messageId on the channel :channelId
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

        $publisher = new Publisher($publisherId);
        $message = $publisher->publishOnChannel(new Channel(new ChannelId($this->channelId)), new BodyMessage("hello"));

        $reflectionClass = new ReflectionClass(Message::class);
        $reflectionProperty = $reflectionClass->getProperty('messageId');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($message, new MessageId($this->messageId));

        //create the message with message repository
        /** @var Container $container */
        $container = $this->getClient()->getContainer();

        /** @var \PostContext\Domain\Repository\PostRepositoryInterface $postRepository */
        $postRepository = $container->get("post_context.infrastructure.post_repository");
        $postRepository->add($message);

        /** @var PublisherRepository $postRepository */
        $publisherRepository = $container->get("post_context.infrastructure.publisher_repository");
        $publisherRepository->add($publisher);
    }

    /**
     * @When the publisher delete the message
     */
    public function thePublisherDeleteTheMessage()
    {
        $this->getClient()->request(
            "DELETE",
            $this->prepareUrl(sprintf("/api/posts/%s", $this->messageId)),
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

        /** @var \PostContext\Domain\Repository\PostRepositoryInterface $postRepository */
        $postRepository = $container->get("post_context.infrastructure.post_repository");

        $collection = $postRepository->get(new MessageId($this->messageId));
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

        /** @var \PostContext\Domain\Repository\PostRepositoryInterface $postRepository */
        $postRepository = $container->get("post_context.infrastructure.post_repository");
        $postRepository->add($message);
    }

    /**
     * @Then the user is informed that is not the owner of that message
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

        /** @var \PostContext\Domain\Repository\PostRepositoryInterface $postRepository */
        $postRepository = $container->get("post_context.infrastructure.post_repository");

        $collection = $postRepository->get(new MessageId($this->messageId));
        PHPUnit_Framework_Assert::assertCount(1, $collection);
    }

}