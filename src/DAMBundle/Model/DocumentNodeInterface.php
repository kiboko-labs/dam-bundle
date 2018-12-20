<?php

namespace Kiboko\Bundle\DAMBundle\Model;

use Doctrine\Common\Collections\Collection;
use Kiboko\Bundle\DAMBundle\Model\Behavior\IdentifiableInterface;
use Kiboko\Bundle\DAMBundle\Model\Behavior\NamedInterface;

interface DocumentNodeInterface extends NamedInterface, IdentifiableInterface
{
    public function getParent(): ?DocumentNodeInterface;

    public function getPath(): PathInterface;

    /**
     * @return Collection|DocumentNodeInterface[]
     */
    public function getNodes(): Collection;

    public function addNode(DocumentNodeInterface $node): void;

    public function removeNode(DocumentNodeInterface $node): void;

    /**
     * @return Collection|DocumentInterface[]
     */
    public function getDocuments(): Collection;

    public function addDocument(DocumentInterface $document): void;

    public function removeDocument(DocumentInterface $document): void;
}
