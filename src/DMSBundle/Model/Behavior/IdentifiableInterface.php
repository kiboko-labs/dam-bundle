<?php

namespace Kiboko\Bundle\DMSBundle\Model\Behavior;

use Ramsey\Uuid\UuidInterface;

interface IdentifiableInterface
{
    public function setId(UuidInterface $id): void;

    public function getId(): ?UuidInterface;
}
