<?php

namespace PostContext\PresentationBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\Serializer;
use PostContext\Application\Handler\MessageHandler;
use PostContext\PresentationBundle\Adapter\DeleteMessageAdapter;
use PostContext\PresentationBundle\Adapter\NewMessageCommandAdapter;
use PostContext\PresentationBundle\Request\DeleteMessageRequest;
use PostContext\PresentationBundle\Request\NewMessageRequest;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as RestController;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends FOSRestController
{
    /** @var MessageHandler  */
    private $messageHandler;

    /** @var Serializer  */
    private $serializer;

    public function __construct(MessageHandler $postHandler, Serializer $serializer)
    {
        $this->messageHandler = $postHandler;
        $this->serializer = $serializer;
    }

    public function postPostAction(Request $request)
    {
        $messageCommandAdapter = new NewMessageCommandAdapter();

        $postPublisherCommand = $messageCommandAdapter->createCommandFromRequest(
            new NewMessageRequest(json_decode($request->getContent(), true))
        );

        $post = $this->messageHandler->postNewMessage(
            $postPublisherCommand
        );

        return $this->view($post, Response::HTTP_CREATED);
    }

    public function deletePostAction(Request $request, $postId)
    {
        $deleteMessageAdapter = new DeleteMessageAdapter();

        $deleteMessageCommand = $deleteMessageAdapter->createCommandFromRequest(
            new DeleteMessageRequest(["post_id" => $postId, "publisher_id" => "899"])
        );

        $this->messageHandler->deleteMessage($deleteMessageCommand);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
