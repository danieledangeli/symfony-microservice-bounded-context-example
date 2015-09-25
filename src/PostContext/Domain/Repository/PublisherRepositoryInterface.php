<?php

namespace PostContext\Domain\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use PostContext\Domain\Publisher;
use PostContext\Domain\ValueObjects\PublisherId;

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
