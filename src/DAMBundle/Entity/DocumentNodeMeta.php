<?php

namespace Kiboko\Bundle\DAMBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;

/**
 * @ORM\Entity
 */
class DocumentNodeMeta extends Meta
{
    /**
     * @var Collection|DocumentNodeInterface[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface",
     *      cascade={"ALL"},
     *      mappedBy="metas",
     *      fetch="EXTRA_LAZY",
     * )
     */
    private $nodes;

    public function __construct()
    {
        $this->nodes = new ArrayCollection();
    }

    /**
     * @return Collection|DocumentNodeInterface[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * @param Collection|DocumentNodeInterface[] $nodes
     */
    public function setNodes(Collection $nodes): void
    {
        $this->nodes = $nodes;
    }

    /**
     * @param DocumentNodeInterface $node
     */
    public function addNodes(DocumentNodeInterface $node): void
    {
        $this->nodes->add($node);
    }

    /**
     * @param DocumentNodeInterface $node
     */
    public function removeNodes(DocumentNodeInterface $node): void
    {
        $this->nodes->removeElement($node);
    }
}
