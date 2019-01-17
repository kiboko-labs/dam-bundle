<?php

namespace Kiboko\Bundle\DAMBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;

class DocumentDatagridListener
{
    /**
     * @var EntityManager $em
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param BuildBefore $event
     */
    public function onBuildBefore(BuildBefore $event)
    {
        if ($event->getDatagrid()->getParameters()->get('_parameters')) {
            $uuid = $event->getDatagrid()->getParameters()->get('_parameters');
            $uuid = $uuid['parent'];

            $node = $this->em->getRepository(DocumentNode::class)->findOneBy(['uuid' => $uuid]);
            $event->getDatagrid()->getParameters()->set('parent', $node->getUuid());
        }
    }
}
