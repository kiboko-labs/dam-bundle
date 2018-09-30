<?php

namespace Kiboko\Bundle\DMSBundle\Integration;

use Kiboko\Bundle\DMSBundle\Entity\FlyimgStorage;
use Kiboko\Bundle\DMSBundle\Form\Type\FlyimgAdapterType;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use Oro\Bundle\IntegrationBundle\Entity\Transport as BaseTransport;
use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;

class FlyimgTransport implements TransportInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    public function init(BaseTransport $transportEntity)
    {
        if (!$transportEntity instanceof FlyimgStorage) {
            throw new \InvalidArgumentException(strtr(
                'The transport should be an instance of %expected%, got %actual%.',
                [
                    '%expected%' => FlyimgStorage::class,
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
        return 'Flyimg CDN Storage';
    }

    public function getSettingsFormType()
    {
        return FlyimgAdapterType::class;
    }

    public function getSettingsEntityFQCN()
    {
        return FlyimgStorage::class;
    }
}
