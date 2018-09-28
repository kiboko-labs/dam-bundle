<?php

namespace Kiboko\Bundle\DMSBundle\Model\Behavior;

use Doctrine\Common\Collections\Collection;
use Oro\Bundle\LocaleBundle\Entity\Localization;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;

interface SluggableInterface
{
    public function getSlugs(): Collection;

    /**
     * @param Collection|LocalizedFallbackValue[] $slugs
     */
    public function setSlugs(Collection $slugs): void;

    public function getLocaleSlug(LocalizationHelper $helper, ?Localization $localization = null): ?LocalizedFallbackValue;

    public function addSlug(LocalizedFallbackValue $slug): void;

    public function removeSlug(LocalizedFallbackValue $slug): void;
}
