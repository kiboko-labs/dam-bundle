<?php

namespace Kiboko\Bundle\DMSBundle\Model;

use Doctrine\Common\Collections\Collection;
use Kiboko\Bundle\DMSBundle\Model\Behavior\IdentifiableInterface;
use Kiboko\Bundle\DMSBundle\Model\Behavior\NamedInterface;

interface DocumentNodeInterface extends NamedInterface, IdentifiableInterface
{
    public function getParent(): ?DocumentNodeInterface;

    public function getPath(): PathInterface;

    /**
     * @return Collection|self[]
     */
    public function getNodes(): Collection;

    public function addNode(self $node): void;

    public function removeNode(self $node): void;

    /**
     * @return Collection|DocumentInterface[]
     */
    public function getDocuments(): Collection;

    public function addDocument(DocumentInterface $document): void;

    public function removeDocument(DocumentInterface $document): void;
}
