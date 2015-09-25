<?php

namespace PostContext\InfrastructureBundle\Repository\InMemory;

use Doctrine\Common\Collections\ArrayCollection;
use PostContext\Domain\Publisher;
use PostContext\Domain\Repository\PublisherRepositoryInterface;
use PostContext\Domain\ValueObjects\PublisherId;

class PublisherRepository implements PublisherRepositoryInterface
{
    /** @var  ArrayCollection */
    private $publishers;

    public function __construct()
    {
        $this->publishers = new ArrayCollection();
    }
    /**
     * @param PublisherId $publisherId
     * @return ArrayCollection<Publisher> $publishers
     */
    public function get(PublisherId $publisherId)
    {
        return $this->publishers->filter(function (Publisher $p) use ($publisherId) {
            return $p->getId()->sameValueAs($publisherId);
        });
    }

    /**
     * @param Publisher $publisher
     * @return Publisher
     */
    public function add(Publisher $publisher)
    {
        $this->publishers->add($publisher);
        return $publisher;
    }
}
