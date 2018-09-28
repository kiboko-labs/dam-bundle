<?php

namespace Kiboko\Bundle\DMSBundle\Model\Behavior;

use Kiboko\Bundle\DMSBundle\Model\DocumentNodeInterface;

interface MovableInterface
{
    public function moveTo(DocumentNodeInterface $node): void;
}
