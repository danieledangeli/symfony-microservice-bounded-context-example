<?php

namespace MessageContext\Domain\ValueObjects;

use MessageContext\Domain\Exception\PublisherIdNotValidException;
use ValueObjects\StringLiteral\StringLiteral;

final class PublisherId extends StringLiteral
{
    protected $value;

    public function __construct($value = null)
    {
        if (null === $value || empty($value)) {
            throw new PublisherIdNotValidException();
        }

        parent::__construct((string) $value);
    }
}
