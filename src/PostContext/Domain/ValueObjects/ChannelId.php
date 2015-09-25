<?php

namespace PostContext\Domain\ValueObjects;

use PostContext\Domain\Exception\PublisherIdNotValidException;
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
