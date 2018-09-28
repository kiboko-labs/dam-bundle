<?php

namespace Kiboko\Bundle\DMSBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kiboko\Bundle\DMSBundle\Model\DocumentInterface;

/**
 * @ORM\Entity
 */
class DocumentAuthorization extends Authorization
{
    /**
     * @var Collection|DocumentInterface[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Kiboko\Bundle\DMSBundle\Model\DocumentInterface",
     *      cascade={"ALL"},
     *      orphanRemoval=true,
     *      mappedBy="authorizations"
     * )
     */
    private $documents;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    /**
     * @return Collection|DocumentInterface[]
     */
    public function getNodes()
    {
        return $this->documents;
    }

    /**
     * @param Collection|DocumentInterface[] $documents
     */
    public function setNodes(Collection $documents): void
    {
        $this->documents = $documents;
    }

    /**
     * @param DocumentInterface $document
     */
    public function addNodes(DocumentInterface $document): void
    {
        $this->documents->add($document);
    }

    /**
     * @param DocumentInterface $document
     */
    public function removeNodes(DocumentInterface $document): void
    {
        $this->documents->removeElement($document);
    }
}
