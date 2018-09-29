<?php

namespace Kiboko\Bundle\DMSBundle\Model;

use Doctrine\Common\Collections\Collection;
use Kiboko\Bundle\DMSBundle\Model\Behavior\IdentifiableInterface;
use Kiboko\Bundle\DMSBundle\Model\Behavior\NamedInterface;
use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;

interface DocumentInterface extends NamedInterface, IdentifiableInterface
{
    public function setFile(File $file): void;

    public function getFile(): ?File;

    public function getNode(): ?DocumentNodeInterface;

    public function getMimeType(): string;

    public function getPath(): PathInterface;
}
