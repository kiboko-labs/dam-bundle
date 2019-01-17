<?php

namespace Kiboko\Bundle\DAMBundle\EventListener;

use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Kiboko\Bundle\DAMBundle\Repository\DocumentNodeRepository;
use Knp\Menu\ItemInterface;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EntityConfigBundle\Config\ConfigInterface;
use Oro\Bundle\EntityConfigBundle\Provider\ConfigProvider;
use Oro\Bundle\FeatureToggleBundle\Checker\FeatureChecker;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\NavigationBundle\Event\ConfigureMenuEvent;
use Oro\Bundle\NavigationBundle\Utils\MenuUpdateUtils;
use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

final class NavigationListener
{
    /**
     * @var LocalizationHelper
     */
    private $localizationHelper;

    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * @var ConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var TokenAccessorInterface
     */
    private $tokenAccessor;

    /**
     * @var AclHelper
     */
    private $aclHelper;

    /**
     * @var FeatureChecker
     */
    private $featureChecker;

    /**
     * @param LocalizationHelper     $localizationHelper
     * @param DoctrineHelper         $doctrineHelper
     * @param ConfigProvider         $entityConfigProvider
     * @param TokenAccessorInterface $tokenAccessor
     * @param AclHelper              $aclHelper
     * @param FeatureChecker         $featureChecker
     */
    public function __construct(
        LocalizationHelper $localizationHelper,
        DoctrineHelper $doctrineHelper,
        ConfigProvider $entityConfigProvider,
        TokenAccessorInterface $tokenAccessor,
        AclHelper $aclHelper,
        FeatureChecker $featureChecker
    ) {
        $this->localizationHelper = $localizationHelper;
        $this->doctrineHelper = $doctrineHelper;
        $this->entityConfigProvider = $entityConfigProvider;
        $this->tokenAccessor = $tokenAccessor;
        $this->aclHelper = $aclHelper;
        $this->featureChecker = $featureChecker;
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    public function onNavigationConfigure(ConfigureMenuEvent $event)
    {
        if (!$this->tokenAccessor->hasUser()) {
            return;
        }

        $storagesMenuItem = MenuUpdateUtils::findMenuItem($event->getMenu(), 'kiboko_dam_team_storages');
        if (!$storagesMenuItem || !$storagesMenuItem->isDisplayed()) {
            return;
        }

        /** @var DocumentNodeRepository $repository */
        $repository = $this->doctrineHelper
            ->getEntityRepositoryForClass(DocumentNode::class);
        $qb = $repository->getRootNodesQueryBuilder();

        /** @var DocumentNodeInterface[] $storages */
        $storages = $this->aclHelper->apply($qb)->getResult();
        if (!$storages) {
            return;
        }
        $this->addDivider($storagesMenuItem);

        foreach ($storages as $storage) {
            $storagesMenuItem->addChild(
                'kiboko_dam_storage_' . $storage->getUuid()->toString(),
                [
                    'label' => $storage->getLocaleName($this->localizationHelper)->getString(),
                    'route' => 'kiboko_dam_node_browse',
                    'routeParameters' => [
                        'node' => $storage->getUuid()->toString(),
                    ],
                ]
            );
        }
        return;

        $storageMenuData = [];
        foreach ($storages as $storage) {
            $config = $this->entityConfigProvider->getConfig($storage['entity']);
            if ($this->checkAvailability($config)) {
                $entityLabel = $config->get('plural_label');
                if (!isset($storageMenuData[$entityLabel])) {
                    $storageMenuData[$entityLabel] = [];
                }
                $storageMenuData[$entityLabel][$storage['id']] = $storage['name'];
            }
        }
        ksort($storageMenuData);
        $this->buildStorageMenu($storagesMenuItem, $storageMenuData);
    }

    /**
     * Checks whether an entity with given config could be shown within navigation of reports
     *
     * @param ConfigInterface $config
     *
     * @return bool
     */
    private function checkAvailability(ConfigInterface $config)
    {
        return true;
    }

    /**
     * Build report menu
     *
     * @param ItemInterface $storageItem
     * @param array         $storageData
     *  key => entity label
     *  value => array of reports id's and label's
     */
    private function buildStorageMenu(ItemInterface $storageItem, $storageData)
    {
        foreach ($storageData as $entityLabel => $storageDatum) {
            foreach ($storageDatum as $storageId => $storageLabel) {
                $this->getEntityMenuItem($storageItem, $entityLabel)
                    ->addChild(
                        $storageLabel . '_report',
                        [
                            'label'           => $storageLabel,
                            'route'           => 'kiboko_dam_node_browse',
                            'routeParameters' => [
                                'node' => $storageId,
                            ],
                        ]
                    );
            }
        }
    }

    /**
     * Adds a divider to the given menu
     *
     * @param ItemInterface $menu
     */
    private function addDivider(ItemInterface $menu)
    {
        $menu->addChild('divider-' . rand(1, 99999))
            ->setLabel('')
            ->setExtra('divider', true)
            ->setExtra('position', 15); // after manage report, we have 10 there
    }

    /**
     * Get entity menu item for report item
     *
     * @param ItemInterface $reportItem
     * @param string        $entityLabel
     * @return ItemInterface
     */
    private function getEntityMenuItem(ItemInterface $reportItem, $entityLabel)
    {
        $entityItemName = $entityLabel . '_report_tab';
        $entityItem     = $reportItem->getChild($entityItemName);
        if (!$entityItem) {
            $reportItem->addChild(
                $entityItemName,
                [
                    'label' => $entityLabel,
                    'uri'   => '#',
                    // after divider, all entities will be added in EntityName:ASC order
                    'extras'=> ['position' => 20],
                ]
            );
            $entityItem = $reportItem->getChild($entityItemName);
        }

        return $entityItem;
    }
}
