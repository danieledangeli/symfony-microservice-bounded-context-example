<?php

namespace MessageContext\Domain\ValueObjects;

use MessageContext\Domain\Exception\PublisherIdNotValidException;
use ValueObjects\StringLiteral\StringLiteral;

final class ChannelId extends StringLiteral
{
    protected $value;

    public function __construct($value)
    {
        if (null === $value || empty($value)) {
            throw new PublisherIdNotValidException();
        }

        parent::__construct((string) $value);
    }
}
