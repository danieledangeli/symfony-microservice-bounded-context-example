<?php

namespace MessageContext\PresentationBundle\Tests\Controller;

use JMS\Serializer\Serializer;
use MessageContext\Application\Command\NewMessageInChannelCommand;
use MessageContext\Application\Handler\MessageHandler;
use MessageContext\Domain\ValueObjects\BodyMessage;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\PublisherId;
use MessageContext\PresentationBundle\Controller\MessageController;
use Symfony\Component\HttpFoundation\Request;

class MessageControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $messageHandlerMock;

    /** @var  MessageController */
    private $messageController;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $serializerMock;

    public function setUp()
    {
        $this->messageHandlerMock = $this->getMockBuilder(MessageHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializerMock = $this->getMockBuilder(Serializer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageController = new MessageController($this->messageHandlerMock, $this->serializerMock);
    }

    public function testItCreateNewMessage()
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

        $this->messageHandlerMock->expects($this->once())
            ->method("postNewMessage")
            ->with($this->equalTo(new NewMessageInChannelCommand(new PublisherId("3444"), new ChannelId("22222"), new BodyMessage("message"))))
            ->willReturn(["id" => 1]);

        $view = $this->messageController->postMessageAction($request);

        $this->assertEquals(201, $view->getStatusCode());
        $this->assertEquals('{"id":1}', json_encode($view->getData()));
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

        $this->messageHandlerMock->expects($this->once())
            ->method("postNewMessage")
            ->with($this->equalTo(new NewMessageInChannelCommand(new PublisherId("3444"), new ChannelId("22222"), new BodyMessage("message"))))
            ->willReturn(["id" => 1]);

        $view = $this->messageController->postMessageAction($request);

        $this->assertEquals(400, $view->getStatusCode());
    }
}
