<?php

namespace MessageContext\Application\Handler;

use MessageContext\Application\Command\DeleteMessageCommand;
use MessageContext\Application\Command\NewMessageInChannelCommand;

interface MessageHandlerInterface
{
    public function postNewMessage(NewMessageInChannelCommand $command);
    public function deleteMessage(DeleteMessageCommand $command);
}
