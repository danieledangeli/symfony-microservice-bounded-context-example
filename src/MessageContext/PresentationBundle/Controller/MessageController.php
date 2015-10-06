<?php

namespace MessageContext\PresentationBundle\Controller;

use FOS\RestBundle\Controller\Annotations as RestController;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\Serializer;
use MessageContext\Application\Handler\MessageHandler;
use MessageContext\PresentationBundle\Adapter\DeleteMessageAdapter;
use MessageContext\PresentationBundle\Adapter\NewMessageCommandAdapter;
use MessageContext\PresentationBundle\Request\DeleteMessageRequest;
use MessageContext\PresentationBundle\Request\NewMessageRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends FOSRestController
{
    /** @var MessageHandler  */
    private $messageHandler;

    /** @var Serializer  */
    private $serializer;

    public function __construct(MessageHandler $messageHandler, Serializer $serializer)
    {
        $this->messageHandler = $messageHandler;
        $this->serializer = $serializer;
    }

    public function postMessageAction(Request $request)
    {
        $messageCommandAdapter = new NewMessageCommandAdapter();

        $newMessageCommand = $messageCommandAdapter->createCommandFromRequest(
            new NewMessageRequest(json_decode($request->getContent(), true))
        );

        $message = $this->messageHandler->postNewMessage(
            $newMessageCommand
        );

        return $this->view($message, Response::HTTP_CREATED);
    }

    public function deleteMessageAction(Request $request, $messageId)
    {
        $deleteMessageAdapter = new DeleteMessageAdapter();

        $deleteMessageCommand = $deleteMessageAdapter->createCommandFromRequest(
            new DeleteMessageRequest(["message_id" => $messageId, "publisher_id" => "899"])
        );

        $this->messageHandler->deleteMessage($deleteMessageCommand);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
