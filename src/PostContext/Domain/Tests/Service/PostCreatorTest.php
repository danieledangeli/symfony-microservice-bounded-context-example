<?php

namespace PostContext\Domain\Tests\Service;

use PostContext\Domain\Exception\ServiceFailureException;
use PostContext\Domain\Exception\ServiceNotAvailableException;
use PostContext\Domain\Message;
use PostContext\Domain\Service\MessageCreator;
use PostContext\Domain\Tests\PostContextDomainUnitTest;
use PostContext\Domain\ValueObjects\ChannelAuthorization;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\BodyMessage;
use PostContext\Domain\ValueObjects\PublisherId;

class PostCreatorTest extends PostContextDomainUnitTest
{
    /** @var  MessageCreator */
    private $postCreator;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $authorizationGateway;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $postRepository;

    public function setUp()
    {
        $this->authorizationGateway = $this->anyAuthorizationGateway();
        $this->postRepository = $this->anyPostRepository();

        $this->postCreator = new MessageCreator($this->authorizationGateway, $this->postRepository);
    }

    public function testItCreateAPost()
    {
        $postToCreate = $this->anyPost();
        $message = new BodyMessage("my message in post");

        $publisherId = new PublisherId("7238r7wjcnsjk");
        $channelId = new ChannelId("12");

        $publisher = $this->anyPublisherWithId($publisherId);
        $channel = $this->anyChannelWithId($channelId);

        $this->thePublisherCanPublishOnChannel($publisherId, $channelId);

        $publisher->expects($this->once())
            ->method("publishOnChannel")
            ->with($this->equalTo($channel), $message)
            ->willReturn($postToCreate);

        $this->postRepository->expects($this->once())
            ->method("add")
            ->with($postToCreate)
            ->willReturn($postToCreate);

        $post = $this->postCreator->createMessage($publisher, $channel, $message);

        $this->assertSame($postToCreate, $post);
    }

    /**
     * @expectedException \PostContext\Domain\Exception\PublisherNotAuthorizedException
     */
    public function testItRaiseExceptionIfPublisherIsNotAuthorizedToPublishOnChannel()
    {
        $message = new BodyMessage("my unpublished message in post");

        $publisherId = new PublisherId("7238r7wjcnsjk");
        $channelId = new ChannelId("12");

        $publisher = $this->anyPublisherWithId($publisherId);
        $channel = $this->anyChannelWithId($channelId);

        $this->thePublisherCannotPublishOnChannel($publisherId, $channelId);

        $publisher->expects($this->never())
            ->method("publishOnChannel");

        $this->postRepository->expects($this->never())
            ->method("add");

        $this->postCreator->createMessage($publisher, $channel, $message);
    }

    /**
     * @dataProvider getFailMicroServicesExceptions
     * @expectedException \PostContext\Domain\Exception\UnableToPerformActionOnChannel
     */
    public function testItRaiseExceptionIfAuthorizationServiceIsNotAvailable($e)
    {
        $message = new BodyMessage("my unpublished message in post");

        $publisherId = new PublisherId("7238r7wjcnsjk");
        $channelId = new ChannelId("12");

        $publisher = $this->anyPublisherWithId($publisherId);
        $channel = $this->anyChannelWithId($channelId);

        $this->authorizationGateway->expects($this->once())
            ->method("getChannelAuthorization")
            ->willThrowException($e);

        $publisher->expects($this->never())
            ->method("publishOnChannel");

        $this->postRepository->expects($this->never())
            ->method("add");

        $this->postCreator->createMessage($publisher, $channel, $message);
    }

    private function thePublisherCanPublishOnChannel($publisherId, $channelId)
    {
        $this->authGatewayWithPublisherIdAndChannelIdReturn(
            $publisherId,
            $channelId,
            new ChannelAuthorization($publisherId, $channelId, true)
        );
    }

    private function thePublisherCannotPublishOnChannel($publisherId, $channelId)
    {
        $this->authGatewayWithPublisherIdAndChannelIdReturn(
            $publisherId,
            $channelId,
            new ChannelAuthorization($publisherId, $channelId, false)
        );
    }

    private function authGatewayWithPublisherIdAndChannelIdReturn($publisherId, $channelId, $return)
    {
        $this->authorizationGateway->expects($this->once())
            ->method("getChannelAuthorization")
            ->with($this->equalTo($publisherId), $this->equalTo($channelId))
            ->willReturn($return);
    }

    public function getFailMicroServicesExceptions()
    {
        return [
            [new ServiceFailureException()],
            [new ServiceNotAvailableException()]
        ];
    }
}
