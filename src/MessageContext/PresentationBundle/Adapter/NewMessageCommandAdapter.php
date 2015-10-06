<?php

namespace MessageContext\PresentationBundle\Adapter;

use MessageContext\Application\Command\NewMessageInChannelCommand;
use MessageContext\Domain\ValueObjects\BodyMessage;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\PublisherId;
use MessageContext\PresentationBundle\Request\NewMessageRequest;

class NewMessageCommandAdapter
{
    /**
     * @param NewMessageRequest $request
     * @return NewMessageInChannelCommand
     */
    public function createCommandFromRequest(NewMessageRequest $request)
    {
        $parameters = $request->getRequestParameters();

        $command = new NewMessageInChannelCommand(
            new PublisherId($parameters["publisher_id"]),
            new ChannelId($parameters["channel_id"]),
            new BodyMessage($parameters["message"])
        );

        return $command;
    }
}
