<?php

namespace PostContext\Application\Handler;

use PostContext\Application\Command\DeleteMessageCommand;
use PostContext\Application\Command\NewMessageInChannelCommand;
use PostContext\Application\Command\PublisherDeletePostCommand;
use PostContext\Domain\Exception\MessageNotFoundException;
use PostContext\Domain\Exception\PublisherMessageNotFoundException;
use PostContext\Domain\Repository\PostRepositoryInterface;
use PostContext\Domain\Service\ChannelFetcher;
use PostContext\Domain\Service\MessageCreator;
use PostContext\Domain\Service\PublisherFetcher;

class MessageHandler implements MessageHandlerInterface
{
    private $messageCreator;
    private $channelFetcher;
    private $publisherFetcher;
    private $messageRepository;

    public function __construct(
        MessageCreator $messageCreator,
        ChannelFetcher $channelFetcher,
        PublisherFetcher $publisherFetcher,
        PostRepositoryInterface $messageRepository)
    {
        $this->messageCreator = $messageCreator;
        $this->channelFetcher = $channelFetcher;
        $this->publisherFetcher = $publisherFetcher;
        $this->messageRepository = $messageRepository;
    }

    public function postNewMessage(NewMessageInChannelCommand $publisherMessageInChannel)
    {
        $channel = $this->channelFetcher->fetchChannel($publisherMessageInChannel->channelId);
        $publisher = $this->publisherFetcher->fetchPublisher($publisherMessageInChannel->publisherId);

        $post = $this->messageCreator->createMessage(
            $publisher,
            $channel,
            $publisherMessageInChannel->message
        );

        return $post;
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
