<?php

namespace MessageContext\InfrastructureBundle\Repository\InMemory;

use Doctrine\Common\Collections\ArrayCollection;
use MessageContext\Domain\Publisher;
use MessageContext\Domain\Repository\PublisherRepositoryInterface;
use MessageContext\Domain\ValueObjects\PublisherId;

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
