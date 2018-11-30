<?php

namespace Kiboko\Bundle\DAMBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;

class DocumentNodeRepository extends NestedTreeRepository
{
    public function findByPath(string $rootSlug, string $nodePath): DocumentNodeInterface
    {
        $qb = $this->getRootNodesQueryBuilder();

        $qb->innerJoin('node.slugs', 'slug')
            ->where($qb->expr()->eq('slug.string', ':slug'))
            ->andWhere($qb->expr()->isNull('slug.localization'))
            ->setMaxResults(1)
        ;

        $result = new ArrayCollection($qb->getQuery()->execute([
            'slug' => $rootSlug,
        ]));

        return $result->first();
    }
}
