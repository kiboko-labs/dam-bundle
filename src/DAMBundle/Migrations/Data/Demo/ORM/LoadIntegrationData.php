<?php

namespace Kiboko\Bundle\DAMBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Kiboko\Bundle\DAMBundle\Entity\LocalStorage;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

class LoadIntegrationData extends AbstractFixture
{
    const LOCAL_INTEGRATION = 'dam_demo_local_integration';

    public function load(ObjectManager $manager)
    {
        $integration = new Channel();

        $integration->setOrganization(
            $manager->getRepository(Organization::class)->getFirst()
        );
        $integration->setName('Local DAM storage');
        $integration->setEnabled(true);
        $integration->setType('kiboko_dam');

        $manager->persist($integration);

        $this->setReference(self::LOCAL_INTEGRATION, $integration);

        $storage = new LocalStorage();

        $storage->setPath('/tmp');
        $storage->setLock(false);

        $integration->setTransport($storage);

        $manager->persist($storage);

        $manager->flush();
        $manager->clear();
    }
}
