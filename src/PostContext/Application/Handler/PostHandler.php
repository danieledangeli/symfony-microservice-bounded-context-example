<?php

namespace PostContext\Application\Handler;

use PostContext\Application\Command\DeleteMessageCommand;
use PostContext\Application\Command\NewMessageInChannelCommand;
use PostContext\Application\Command\PublisherDeletePostCommand;
use PostContext\Domain\Repository\PostRepositoryInterface;
use PostContext\Domain\Service\ChannelFetcher;
use PostContext\Domain\Service\MessageCreator;
use PostContext\Domain\Service\PublisherFetcher;

class PostHandler implements PostHandlerInterface
{
    private $postCreator;
    private $channelFetcher;
    private $publisherFetcher;
    private $postRepository;

    public function __construct(
        MessageCreator $postCreator,
        ChannelFetcher $channelFetcher,
        PublisherFetcher $publisherFetcher,
        PostRepositoryInterface $postRepository)
    {
        $this->postCreator = $postCreator;
        $this->channelFetcher = $channelFetcher;
        $this->publisherFetcher = $publisherFetcher;
        $this->postRepository = $postRepository;
    }

    public function postNewMessage(NewMessageInChannelCommand $publisherPostInChannel)
    {
        $channel = $this->channelFetcher->fetchChannel($publisherPostInChannel->channelId);
        $publisher = $this->publisherFetcher->fetchPublisher($publisherPostInChannel->publisherId);

        $post = $this->postCreator->createMessage(
            $publisher,
            $channel,
            $publisherPostInChannel->message
        );

        return $post;
    }

    public function deleteMessage(DeleteMessageCommand $deleteMessageCommand)
    {
        $publisher = $this->publisherFetcher->fetchPublisher($deleteMessageCommand->publisherId);
        $publisher->deleteMessage($deleteMessageCommand->postId);
        $this->postRepository->remove($deleteMessageCommand->postId);
    }
}
