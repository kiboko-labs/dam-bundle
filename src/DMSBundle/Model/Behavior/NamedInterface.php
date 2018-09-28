<?php

namespace Kiboko\Bundle\DMSBundle\Model\Behavior;

use Doctrine\Common\Collections\Collection;
use Oro\Bundle\LocaleBundle\Entity\Localization;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;

interface NamedInterface
{
    /**
     * @param Collection|LocalizedFallbackValue[] $names
     */
    public function setNames(Collection $names);

    /**
     * @return Collection|LocalizedFallbackValue[]
     */
    public function getNames(): Collection;

    public function getLocaleName(LocalizationHelper $helper, ?Localization $localization = null): ?LocalizedFallbackValue;

    public function addName(LocalizedFallbackValue $name): void;

    public function removeName(LocalizedFallbackValue $name): void;
}
