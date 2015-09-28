<?php

namespace PostContext\Application\Handler;

use PostContext\Application\Command\DeleteMessageCommand;
use PostContext\Application\Command\NewMessageInChannelCommand;

interface MessageHandlerInterface
{
    public function postNewMessage(NewMessageInChannelCommand $command);
    public function deleteMessage(DeleteMessageCommand $command);
}
