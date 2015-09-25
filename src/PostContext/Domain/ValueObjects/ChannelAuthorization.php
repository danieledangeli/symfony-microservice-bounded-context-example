<?php

namespace PostContext\Domain\ValueObjects;

use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\PublisherId;
use ValueObjects\ValueObjectInterface;

class ChannelAuthorization implements ValueObjectInterface
{
    private $publisherId;
    private $channelId;
    private $isAuthorized;

    public function __construct(PublisherId $publisherId, ChannelId $channelId, $isAuthorized)
    {
        $this->channelId = $channelId;
        $this->publisherId = $publisherId;
        $this->isAuthorized = $isAuthorized;
    }

    /**
     * @return boolean
     */
    public function canPublisherPublishOnChannel()
    {
        return $this->isAuthorized;
    }

    /**
     * Returns a object taking PHP native value(s) as argument(s).
     *
     * @return ValueObjectInterface
     */
    public static function fromNative()
    {
        $pId = func_get_arg(0);
        $cId = func_get_arg(1);
        $isAuth = func_get_arg(2);

        return new self(new PublisherId($pId), new ChannelId($cId), $isAuth);
    }

    /**
     * Compare two ValueObjectInterface and tells whether they can be considered equal
     *
     * @param  ValueObjectInterface $object
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $object)
    {
        return (string) $this === (string) $object;
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf("Publisher: %s|Channel: %s|Authorized: %s",
            $this->publisherId,
            $this->channelId,
            $this->isAuthorized
        );
    }
}
