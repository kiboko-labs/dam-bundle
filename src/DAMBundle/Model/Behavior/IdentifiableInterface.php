<?php

namespace Kiboko\Bundle\DAMBundle\Model\Behavior;

use Ramsey\Uuid\UuidInterface;

interface IdentifiableInterface
{
    public function setId(UuidInterface $id): void;

    public function getId(): ?UuidInterface;
}
