<?php

namespace Kiboko\Bundle\DAMBundle\Model\Behavior;

use Oro\Bundle\AttachmentBundle\Entity\File;

interface ThumbnailedInterface
{
    public function setThumbnail(File $thumbnail): void;

    public function getThumbnail(): File;
}
