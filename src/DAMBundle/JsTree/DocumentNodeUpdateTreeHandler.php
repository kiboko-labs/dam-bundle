<?php

namespace Kiboko\Bundle\DAMBundle\JsTree;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Kiboko\Bundle\DAMBundle\Entity\TeamStorageNode;
use Kiboko\Bundle\DAMBundle\Model\Behavior\MovableInterface;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\UIBundle\Model\TreeItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class DocumentNodeUpdateTreeHandler
{
    const ROOT_PARENT_VALUE = '#';

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var LocalizationHelper
     */
    private $localizationHelper;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param TranslatorInterface $translator
     * @param LocalizationHelper $localizationHelper
     * @param EntityManager $entityManager
     */
    public function __construct(
        TranslatorInterface $translator,
        LocalizationHelper $localizationHelper,
        EntityManager $entityManager
    ) {
        $this->translator = $translator;
        $this->localizationHelper = $localizationHelper;
        $this->entityManager = $entityManager;
    }

    /**
     * @param TeamStorageNode $root
     *
     * @return array
     */
    public function createTree(TeamStorageNode $root): array
    {
        if ($root === null) {
            return [];
        }

        $tree = $this->getNodes($root);

        return $this->formatTree($tree, $root);
    }

    /**
     * @param DocumentNodeInterface $node
     *
     * @return array
     */
    private function getNodes(DocumentNodeInterface $node)
    {
        $nodes = [];

        /** @var DocumentNodeInterface $child */
        foreach ($node->getNodes() as $child) {
            $nodes[] = $child;
            $nodes = array_merge($nodes, $this->getNodes($child));
        }

        return $nodes;
    }

    /**
     * @param DocumentNodeInterface[] $entities
     * @param DocumentNodeInterface   $root
     *
     * @return array
     */
    private function formatTree(array $entities, DocumentNodeInterface $root)
    {
        $formattedTree = [];

        foreach ($entities as $entity) {
            $node = $this->formatEntity($root, $entity);

            $formattedTree[] = $node;
        }

        $topNode = $this->formatEntity($root,$root);
        $topNode['parent'] = self::ROOT_PARENT_VALUE;
        $formattedTree[] = $topNode;

        return $formattedTree;
    }

    private function getLabel(DocumentNodeInterface $entity): string
    {
        $name = $entity->getLocaleName($this->localizationHelper);
        if ($name === null) {
            return '';
        }
        return $name->getString();
    }

    private function buildCode(DocumentNodeInterface $entity): string
    {
        return sprintf('node_%s', str_replace('-', '_', $entity->getUuid()->toString()));
    }

    /**
     * @param TeamStorageNode       $root
     * @param DocumentNodeInterface $entity
     * @param bool                  $isOpened
     *
     * @return array
     */
    private function formatEntity(TeamStorageNode $root, DocumentNodeInterface $entity, bool $isOpened = false)
    {
        return [
            'id' => $this->buildCode($entity),
            'uuid' => $entity->getUuid()->toString(),
            'storage' => $root->getUuid()->toString(),
            'parent' => $entity->getParent() ? $this->buildCode($entity->getParent()) : self::ROOT_PARENT_VALUE,
            'text' => $this->getLabel($entity),
            'state' => [
                'opened' => $isOpened,
                'disabled' => false,
            ],
            //'li_attr' => !$entity->isDisplayed() ? ['class' => 'hidden'] : []
        ];
    }

    /**
     * @param DocumentNodeInterface|null $root
     *
     * @return TreeItem[]
     */
    public function getTreeItemList(DocumentNodeInterface $root = null)
    {
        $nodes = $this->createTree($root);

        $items = [];

        foreach ($nodes as $node) {
            $items[$node['id']] = new TreeItem($node['id'], $node['text']);
        }

        foreach ($nodes as $node) {
            if (array_key_exists($node['parent'], $items)) {
                /** @var TreeItem $treeItem */
                $treeItem = $items[$node['id']];
                $treeItem->setParent($items[$node['parent']]);
            }
        }

        return $items;
    }

    /**
     * @param TreeItem[] $sourceData
     * @param array      $treeData
     */
    public function disableTreeItems(array $sourceData, array &$treeData)
    {
        foreach ($treeData as &$treeItem) {
            foreach ($sourceData as $sourceItem) {
                if ($sourceItem->getKey() === $treeItem['id'] || $sourceItem->hasChildRecursive($treeItem['id'])) {
                    $treeItem['state']['disabled'] = true;
                }
            }
        }
    }

    public function createNode(DocumentNodeInterface $parent,string $name) {

        $node = new DocumentNode();
        $node->setParent($parent);

        $nameToApply = new LocalizedFallbackValue();
        $nameToApply->setString($name);
        $nameToApply->setLocalization($this->localizationHelper->getCurrentLocalization());

        $names = new ArrayCollection();
        $names->add($nameToApply);

        $slugs = new ArrayCollection();
        $slugs->add($nameToApply);

        $node->setNames($names);
        $node->setSlugs($slugs);

        try {
            $this->entityManager->persist($node);
            $this->entityManager->flush();
        }
        catch (ORMException $e) {
            return new JsonResponse($e->getMessage(),500);
        }

        return new JsonResponse('created',200);

    }

    public function moveNode(MovableInterface $node, MovableInterface $newParent)
    {
        if (!$newParent instanceof DocumentNodeInterface || !$node instanceof DocumentNodeInterface)
        {
            return new JsonResponse('Arguments are not an instance of MovableInterface',500);

        }
        $node->moveTo($newParent);

        try {
            $this->entityManager->persist($node);
            $this->entityManager->flush();
        }
        catch (ORMException $e) {
            return new JsonResponse($e->getMessage(),500);
        }

        return new JsonResponse('moved folder successfully',200);

    }
}
