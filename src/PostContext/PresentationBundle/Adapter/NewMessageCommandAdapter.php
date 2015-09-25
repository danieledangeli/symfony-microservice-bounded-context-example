<?php

namespace PostContext\PresentationBundle\Adapter;

use PostContext\Application\Command\NewMessageInChannelCommand;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\BodyMessage;
use PostContext\Domain\ValueObjects\PublisherId;
use PostContext\PresentationBundle\Request\NewMessageRequest;

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
