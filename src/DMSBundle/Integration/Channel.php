<?php

namespace Kiboko\Bundle\DMSBundle\Integration;

use Oro\Bundle\IntegrationBundle\Provider\ChannelInterface;
use Oro\Bundle\IntegrationBundle\Provider\IconAwareIntegrationInterface;

class Channel implements ChannelInterface, IconAwareIntegrationInterface
{
    public function getLabel()
    {
        return 'Kiboko DMS';
    }

    public function getIcon()
    {
        return 'bundles/kibokodms/image/logo.png';
    }
}
