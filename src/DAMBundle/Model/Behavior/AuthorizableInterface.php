<?php

namespace Kiboko\Bundle\DAMBundle\Model\Behavior;

use Doctrine\Common\Collections\Collection;
use Kiboko\Bundle\DAMBundle\Model\AuthorizationInterface;

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
