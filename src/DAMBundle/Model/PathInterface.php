<?php

namespace Kiboko\Bundle\DAMBundle\Model;

interface PathInterface
{
    public function add(string ...$paths): void;

    public function __toString();
}
