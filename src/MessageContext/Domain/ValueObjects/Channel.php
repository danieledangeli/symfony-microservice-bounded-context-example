<?php

namespace MessageContext\Domain\ValueObjects;

use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\PublisherId;
use ValueObjects\ValueObjectInterface;

class Channel implements ValueObjectInterface
{
    private $channelId;
    private $closed;

    public function __construct($channelId, $closed)
    {
        $this->channelId = new ChannelId($channelId);
        $this->closed = $closed;
    }

    public function getId()
    {
        return $this->channelId;
    }

    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * Returns a object taking PHP native value(s) as argument(s).
     *
     * @return ValueObjectInterface
     */
    public static function fromNative()
    {
        $pId = func_get_arg(0);
        $isClosed = func_get_arg(1);

        return new self(new PublisherId($pId), $isClosed);
    }

    /**
     * Compare two ValueObjectInterface and tells whether they can be considered equal
     *
     * @param  ValueObjectInterface $object
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $object)
    {
        if (get_class($object) !== get_class($this)) {
            return false;
        }

        return $this->isClosed() === $object->isClosed() && $this->getId() === $object->getId();
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf("Channel: %s|Closed: %s",
            $this->channelId,
            $this->closed
        );
    }
}
