<?php

namespace PostContext\PresentationBundle\Adapter;

use PostContext\Application\Command\DeleteMessageCommand;
use PostContext\Domain\ValueObjects\MessageId;
use PostContext\Domain\ValueObjects\PublisherId;
use PostContext\PresentationBundle\Request\DeleteMessageRequest;

class DeleteMessageAdapter
{
    public function createCommandFromRequest(DeleteMessageRequest $request)
    {
        $parameters = $request->getRequestParameters();

        $deleteMessageCommand = new DeleteMessageCommand(
            new PublisherId($parameters["publisher_id"]),
            new MessageId($parameters["post_id"])
        );

        return $deleteMessageCommand;
    }
}
