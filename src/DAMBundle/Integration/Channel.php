<?php

namespace Kiboko\Bundle\DAMBundle\Integration;

use Oro\Bundle\IntegrationBundle\Provider\ChannelInterface;
use Oro\Bundle\IntegrationBundle\Provider\IconAwareIntegrationInterface;

class Channel implements ChannelInterface, IconAwareIntegrationInterface
{
    public function getLabel()
    {
        return 'Kiboko DAM';
    }

    public function getIcon()
    {
        return 'bundles/kibokodam/image/logo.png';
    }
}
