<?php

namespace PostContext\Domain\Service;

use PostContext\Domain\Channel;
use PostContext\Domain\Exception\MicroServiceIntegrationException;
use PostContext\Domain\Exception\PublisherNotAuthorizedException;
use PostContext\Domain\Exception\UnableToPerformActionOnChannel;
use PostContext\Domain\Gateway\ChannelAuthorizationGatewayInterface;
use PostContext\Domain\Message;
use PostContext\Domain\Publisher;
use PostContext\Domain\Repository\PostRepositoryInterface;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\BodyMessage;
use PostContext\Domain\ValueObjects\PublisherId;

class MessageCreator
{
    private $channelAuthGateway;
    private $postRepository;

    public function __construct(ChannelAuthorizationGatewayInterface $channelAuthGateway, PostRepositoryInterface $postRepository)
    {
        $this->channelAuthGateway = $channelAuthGateway;
        $this->postRepository = $postRepository;
    }

    /**
     * @param Publisher $publisher
     * @param Channel $channel
     * @param $message
     * @return Message $post
     *
     * @throws PublisherNotAuthorizedException
     */
    public function createMessage(Publisher $publisher, Channel $channel, BodyMessage $message)
    {
        $publisherId = $publisher->getId();
        $channelId = $channel->getId();

        if ($this->canPublisherPublishInChannel($publisherId, $channelId)) {
            $post = $publisher->publishOnChannel($channel, $message);

            return $this->postRepository->add($post);
        }

        throw new PublisherNotAuthorizedException(sprintf("The publisher %s is not authorized to publish on channel %s",
            $publisherId->toNative(),
            $channelId->toNative()
        ));
    }

    /**
     * @param PublisherId $publisherId
     * @param ChannelId $channelId
     * @return mixed
     * @throws UnableToPerformActionOnChannel
     */
    private function canPublisherPublishInChannel(PublisherId $publisherId, ChannelId $channelId)
    {
        try {
            $channelAuthorization = $this->channelAuthGateway->getChannelAuthorization($publisherId, $channelId);
            return $channelAuthorization->canPublisherPublishOnChannel();
        } catch (MicroServiceIntegrationException $e) {
            //the rule service is not available
            //A. the user can publish in the channel
            //B. the user cannot publish in the channel

            throw new UnableToPerformActionOnChannel(
                sprintf("Impossible to create post in the channel %s at the moment", $channelId)
            );
        }
    }
}
