<?php

namespace MessageContext\Domain\ValueObjects;

use ValueObjects\StringLiteral\StringLiteral;

class BodyMessage extends StringLiteral
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
