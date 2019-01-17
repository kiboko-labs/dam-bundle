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
     * @param DocumentNodeInterface $node
     * @return array
     */
    public function createTree(TeamStorageNode $root, DocumentNodeInterface $node = null): array
    {
        if ($root === null) {
            return [];
        }

        $tree = $this->getNodes($root);

        return $this->formatTree($tree, $root, $node);
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
     * @param DocumentNodeInterface $root
     *
     * @param DocumentNodeInterface $node
     * @return array
     */
    private function formatTree(array $entities, DocumentNodeInterface $root, DocumentNodeInterface $node = null)
    {
        $formattedTree = [];
        $uuidOpenedNode = null;
        foreach ($entities as $entity) {
            if ($entity === $node) {
                $node = $this->formatEntity($root, $entity, true, true);
            } else {
                $node = $this->formatEntity($root, $entity);
            }

            if ($entity->getParent() === $root) {
                $node['parent'] = self::ROOT_PARENT_VALUE;
            }

            $formattedTree[] = $node;
        }

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
     * @param bool                  $isSelected
     *
     * @return array
     */
    private function formatEntity(
        TeamStorageNode $root,
        DocumentNodeInterface $entity,
        bool $isOpened = false,
        bool $isSelected = false
    ) {
        return [
            'id' => $this->buildCode($entity),
            'uuid' => $entity->getUuid()->toString(),
            'storage' => $root->getUuid()->toString(),
            'parentUuid' => $entity->getParent()->getUuid(),
            'parent' => $entity->getParent() ? $this->buildCode($entity->getParent()) : self::ROOT_PARENT_VALUE,
            'text' => $this->getLabel($entity),
            'state' => [
                'opened' => $isOpened,
                'disabled' => false,
                'selected' => $isSelected,
            ],
            //'li_attr' => !$entity->isDisplayed() ? ['class' => 'hidden'] : []
        ];
    }

    /**
     * @param DocumentNodeInterface|null $root
     *
     * @param DocumentNodeInterface|null $node
     * @return TreeItem[]
     */
    public function getTreeItemList(DocumentNodeInterface $root = null, DocumentNodeInterface $node = null)
    {
        $nodes = $this->createTree($root,$node);

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
        } catch (ORMException $e) {
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
        } catch (ORMException $e) {
            return new JsonResponse($e->getMessage(),500);
        }

        return new JsonResponse('moved folder successfully',200);

    }
}
