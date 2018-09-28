<?php

namespace Kiboko\Bundle\DMSBundle\Model\Behavior;

use Doctrine\Common\Collections\Collection;
use Kiboko\Bundle\DMSBundle\Model\MetaInterface;
use Oro\Bundle\LocaleBundle\Entity\Localization;

interface MetadatableInterface
{
    public function getMetas(): Collection;

    /**
     * @param Collection|MetaInterface[] $slugs
     */
    public function setMetas(Collection $slugs): void;

    public function getLocaleMetas(?Localization $localization = null): Collection;

    public function addMeta(MetaInterface $slug): void;

    public function removeMeta(MetaInterface $slug): void;
}
