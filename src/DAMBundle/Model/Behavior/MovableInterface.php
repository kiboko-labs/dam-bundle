<?php

namespace Kiboko\Bundle\DAMBundle\Model\Behavior;

use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;

interface MovableInterface
{
    public function moveTo(DocumentNodeInterface $node): void;
}
