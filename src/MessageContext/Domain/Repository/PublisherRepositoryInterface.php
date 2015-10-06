<?php

namespace MessageContext\Domain\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use MessageContext\Domain\Publisher;
use MessageContext\Domain\ValueObjects\PublisherId;

interface PublisherRepositoryInterface
{
    /**
     * @param PublisherId $publisherId
     * @return ArrayCollection<Publisher> $publishers
     */
    public function get(PublisherId $publisherId);

    /**
     * @param Publisher $publisher
     * @return void
     */
    public function add(Publisher $publisher);
}
