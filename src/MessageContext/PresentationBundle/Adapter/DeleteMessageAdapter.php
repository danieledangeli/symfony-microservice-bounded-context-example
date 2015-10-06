<?php

namespace MessageContext\PresentationBundle\Adapter;

use MessageContext\Application\Command\DeleteMessageCommand;
use MessageContext\Domain\ValueObjects\MessageId;
use MessageContext\Domain\ValueObjects\PublisherId;
use MessageContext\PresentationBundle\Request\DeleteMessageRequest;

class DeleteMessageAdapter
{
    public function createCommandFromRequest(DeleteMessageRequest $request)
    {
        $parameters = $request->getRequestParameters();

        $deleteMessageCommand = new DeleteMessageCommand(
            new PublisherId($parameters["publisher_id"]),
            new MessageId($parameters["message_id"])
        );

        return $deleteMessageCommand;
    }
}
