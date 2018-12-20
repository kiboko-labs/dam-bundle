<?php

namespace Kiboko\Bundle\DAMBundle\JsTree\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

interface CommandInterface
{
    public function execute(ObjectManager $em, ValidatorInterface $validator): DocumentNodeInterface;
}
