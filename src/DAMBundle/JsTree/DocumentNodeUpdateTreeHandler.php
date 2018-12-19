<?php

namespace Kiboko\Bundle\DAMBundle\JsTree;

use Kiboko\Bundle\DAMBundle\Entity\TeamStorageNode;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\UIBundle\Model\TreeItem;
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
     * @param TranslatorInterface $translator
     * @param LocalizationHelper  $localizationHelper
     */
    public function __construct(
        TranslatorInterface $translator,
        LocalizationHelper $localizationHelper
    ) {
        $this->translator = $translator;
        $this->localizationHelper = $localizationHelper;
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

    public function getDocuments(TeamStorageNode $node)
    {
        $documents = $node->getDocuments()->toArray();

        return $documents;
    }
}
