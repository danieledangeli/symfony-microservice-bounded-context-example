<?php

namespace MessageContext\Application\Handler;

use MessageContext\Application\Command\DeleteMessageCommand;
use MessageContext\Application\Command\NewMessageInChannelCommand;
use MessageContext\Application\Exception\MessageNotFoundException;
use MessageContext\Application\Service\ChannelAuthorizationFetcher;
use MessageContext\Application\Service\ChannelFetcher;
use MessageContext\Domain\Repository\MessageRepositoryInterface;
use MessageContext\Application\Service\PublisherFetcher;

class MessageHandler implements MessageHandlerInterface
{
    private $channelAuthorizationFetcher;
    private $channelFetcher;
    private $publisherFetcher;
    private $messageRepository;

    public function __construct(
        ChannelFetcher $channelFetcher,
        PublisherFetcher $publisherFetcher,
        ChannelAuthorizationFetcher $channelAuthorizationFetcher,
        MessageRepositoryInterface $messageRepository)
    {
        $this->channelAuthorizationFetcher = $channelAuthorizationFetcher;
        $this->channelFetcher = $channelFetcher;
        $this->publisherFetcher = $publisherFetcher;
        $this->messageRepository = $messageRepository;
    }

    public function postNewMessage(NewMessageInChannelCommand $publisherMessageInChannel)
    {
        $channel = $this->channelFetcher->fetchChannel($publisherMessageInChannel->channelId);
        $publisher = $this->publisherFetcher->fetchPublisher($publisherMessageInChannel->publisherId);

        $channelAuthorization = $this->channelAuthorizationFetcher->fetchChannelAuthorization(
            $publisherMessageInChannel->publisherId,
            $publisherMessageInChannel->channelId
        );

        $message = $publisher->publishOnChannel($channel, $channelAuthorization, $publisherMessageInChannel->message);

        return $this->messageRepository->add($message);
    }

    public function deleteMessage(DeleteMessageCommand $deleteMessageCommand)
    {
        $publisher = $this->publisherFetcher->fetchPublisher($deleteMessageCommand->publisherId);
        $message = $this->messageRepository->get($deleteMessageCommand->messageId);

        if ($message->count() > 0) {
            $channel = $this->channelFetcher->fetchChannel($message->get(0)->getChannelId());
            $publisher->deleteMessage($channel, $deleteMessageCommand->messageId);
            return $this->messageRepository->remove($deleteMessageCommand->messageId);
        }

        throw new MessageNotFoundException(sprintf("The message has not been found", $deleteMessageCommand->messageId));
    }
}
