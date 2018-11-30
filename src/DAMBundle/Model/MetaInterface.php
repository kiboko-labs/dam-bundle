<?php

namespace Kiboko\Bundle\DAMBundle\Model;

use Oro\Bundle\LocaleBundle\Entity\Localization;

interface MetaInterface
{
    public function getLocalization(): ?Localization;
}
