<?php

namespace Kiboko\Bundle\DAMBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\LocaleBundle\Entity\Localization;

class TeamStorageNodeRepository extends DocumentNodeRepository
{
    public function findBySlug(string $slug, ?Localization $localization = null): DocumentNodeInterface
    {
        $qb = $this->getRootNodesQueryBuilder();

        $qb->innerJoin('node.slugs', 'slug')
            ->where($qb->expr()->eq('slug.string', ':slug'))
            ->setMaxResults(1)
        ;

        if ($localization === null) {
            $qb->andWhere($qb->expr()->isNull('slug.localization'));
        } else {
            $qb->andWhere($qb->expr()->eq('slug.localization', $localization));
        }

        $result = new ArrayCollection($qb->getQuery()->execute([
            'slug' => $slug,
        ]));

        return $result->first();
    }
}
