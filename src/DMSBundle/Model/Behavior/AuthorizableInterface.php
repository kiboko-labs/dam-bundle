<?php

namespace Kiboko\Bundle\DMSBundle\Model\Behavior;

use Doctrine\Common\Collections\Collection;
use Kiboko\Bundle\DMSBundle\Model\AuthorizationInterface;

interface AuthorizableInterface
{
    /**
     * @param Collection|AuthorizationInterface[] $authorizations
     */
    public function setAuthorizations(Collection $authorizations);

    /**
     * @return Collection|AuthorizationInterface[]
     */
    public function getAuthorizations(): Collection;

    public function addAuthorization(AuthorizationInterface $authorization): void;

    public function removeAuthorization(AuthorizationInterface $authorization): void;
}
