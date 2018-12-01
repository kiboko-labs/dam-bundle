<?php

namespace Kiboko\Bundle\DAMBundle\Model\Behavior;

use Ramsey\Uuid\UuidInterface;

interface IdentifiableInterface
{
    public function setUuid(UuidInterface $id): void;

    public function getUuid(): ?UuidInterface;
}
