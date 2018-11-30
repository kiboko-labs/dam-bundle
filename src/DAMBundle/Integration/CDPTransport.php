<?php

namespace Kiboko\Bundle\DAMBundle\Integration;

use Kiboko\Bundle\DAMBundle\Entity\CDPStorage;
use Kiboko\Bundle\DAMBundle\Form\Type\CDPAdapterType;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use Oro\Bundle\IntegrationBundle\Entity\Transport as BaseTransport;
use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;

class CDPTransport implements TransportInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    public function init(BaseTransport $transportEntity)
    {
        if (!$transportEntity instanceof CDPStorage) {
            throw new \InvalidArgumentException(strtr(
                'The transport should be an instance of %expected%, got %actual%.',
                [
                    '%expected%' => CDPStorage::class,
                    '%actual%' => get_class($transportEntity),
                ]
            ));
        }

        $this->adapter = new Local(
            $transportEntity->getUrl(),
            $transportEntity->getLock()
        );
    }

    public function getLabel()
    {
        return 'Kiboko CDP Storage';
    }

    public function getSettingsFormType()
    {
        return CDPAdapterType::class;
    }

    public function getSettingsEntityFQCN()
    {
        return CDPStorage::class;
    }
}
