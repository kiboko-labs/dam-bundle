<?php

namespace Kiboko\Bundle\DMSBundle\Model;

interface PathInterface
{
    public function add(string ...$paths): void;

    public function __toString();
}
