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
     *      mappedBy="authorizations",
     *      fetch="EXTRA_LAZY",
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
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @param Collection|DocumentInterface[] $documents
     */
    public function setDocuments(Collection $documents): void
    {
        $this->documents = $documents;
    }

    /**
     * @param DocumentInterface $document
     */
    public function addDocument(DocumentInterface $document): void
    {
        $this->documents->add($document);
    }

    /**
     * @param DocumentInterface $document
     */
    public function removeDocument(DocumentInterface $document): void
    {
        $this->documents->removeElement($document);
    }
}
