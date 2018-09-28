<?php

namespace Kiboko\Bundle\DMSBundle\Model;

use Oro\Bundle\LocaleBundle\Entity\Localization;

interface MetaInterface
{
    public function getLocalization(): ?Localization;
}
