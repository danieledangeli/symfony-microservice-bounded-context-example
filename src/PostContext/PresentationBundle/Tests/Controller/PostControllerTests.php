<?php

namespace PostContext\PresentationBundle\Tests\Controller;

use PostContext\Application\Command\NewMessageInChannelCommand;
use PostContext\Application\Handler\MessageHandler;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\PublisherId;
use PostContext\PresentationBundle\Controller\MessageController;
use Symfony\Component\HttpFoundation\Request;

class PostControllerTests extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $postHandlerMock;

    /** @var  MessageController */
    private $postController;

    public function setUp()
    {
        $this->postHandlerMock = $this->getMockBuilder(MessageHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->postController = new MessageController($this->postHandlerMock);
    }

    public function testItCreateNewPost()
    {
        $requestBody = <<<JSON
{"publisher_id": "3444","channel_id": "22222","message": "message"}
JSON;

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->once())
            ->method("getContent")
            ->willReturn($requestBody);

        $this->postHandlerMock->expects($this->once())
            ->method("postNewMessage")
            ->with($this->equalTo(new NewMessageInChannelCommand(new PublisherId("3444"), new ChannelId("22222"), "message")))
            ->willReturn(["id" => 1]);

        $response = $this->postController->postMessageAction($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"id":1}', $response->getContent());
    }

    public function testPublisherIdIsRequired()
    {
        $requestBody = <<<JSON
{"publisher_id": "","channel_id": "22222","message": "message"}
JSON;

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->once())
            ->method("getContent")
            ->willReturn($requestBody);

        $this->postHandlerMock->expects($this->once())
            ->method("postNewMessage")
            ->with($this->equalTo(new NewMessageInChannelCommand(new PublisherId("3444"), new ChannelId("22222"), "message")))
            ->willReturn(["id" => 1]);

        $response = $this->postController->postMessageAction($request);

        $this->assertEquals(400, $response->getStatusCode());
    }
}
