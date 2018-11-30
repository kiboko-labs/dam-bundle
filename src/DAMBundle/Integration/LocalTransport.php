<?php

namespace Kiboko\Bundle\DAMBundle\Integration;

use Kiboko\Bundle\DAMBundle\Entity;
use Kiboko\Bundle\DAMBundle\Form\Type\LocalAdapterType;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use Oro\Bundle\IntegrationBundle\Entity\Transport as BaseTransport;
use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;

class LocalTransport implements TransportInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    public function init(BaseTransport $transportEntity)
    {
        if (!$transportEntity instanceof Entity\LocalStorage) {
            throw new \InvalidArgumentException(strtr(
                'The transport should be an instance of %expected%, got %actual%.',
                [
                    '%expected%' => Entity\LocalStorage::class,
                    '%actual%' => get_class($transportEntity),
                ]
            ));
        }

        $this->adapter = new Local(
            $transportEntity->getPath(),
            $transportEntity->getLock()
        );
    }

    public function getLabel()
    {
        return 'Local Storage';
    }

    public function getSettingsFormType()
    {
        return LocalAdapterType::class;
    }

    public function getSettingsEntityFQCN()
    {
        return Entity\LocalStorage::class;
    }
}
