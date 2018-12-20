<?php

namespace Kiboko\Bundle\DAMBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Kiboko\Bundle\DAMBundle\Entity\TeamStorageNode;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\OrganizationBundle\Migrations\Data\ORM\LoadOrganizationAndBusinessUnitData;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Migrations\Data\ORM\LoadRolesData;

class LoadDocumentNodeData extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            LoadIntegrationData::class,
        ];
    }

    private function getIntegration(ObjectManager $manager): Channel
    {
        try {
            return $this->getReference(LoadIntegrationData::LOCAL_INTEGRATION);
        } catch (\OutOfBoundsException $e) {
            return $manager->getRepository(Channel::class)
                ->findOneBy([
                    'type' => 'kiboko_dam',
                    'enabled' => true,
                ]);
        }
    }

    private function getOwner(ObjectManager $manager): User
    {
        $role = $manager->getRepository('OroUserBundle:Role')
            ->findOneBy(['role' => LoadRolesData::ROLE_ADMINISTRATOR]);

        return $manager->getRepository('OroUserBundle:Role')->getFirstMatchedUser($role);
    }

    private function getOrganization(ObjectManager $manager): Organization
    {
        try {
            return $this->getReference(LoadOrganizationAndBusinessUnitData::REFERENCE_DEFAULT_ORGANIZATION);
        } catch (\OutOfBoundsException $e) {
            return $manager->getRepository(Organization::class)
                ->findOneBy([
                    'enabled' => true,
                ]);
        }
    }

    public function load(ObjectManager $manager)
    {
        $nodes = [];

        $tree = $this->buildRoot(
            $this->getIntegration($manager),
            $owner = $this->getOwner($manager),
            $organization = $this->getOrganization($manager),
            $this->buildFallback('E-commerce'),
            $this->buildFallback('ecommerce'),
            [
                $nodes[] = $this->buildNode($owner, $organization, 'Lorem', 'lorem', []),
                $nodes[] = $this->buildNode($owner, $organization, 'Ipsum', 'ipsum', [
                    $nodes[] = $this->buildNode($owner, $organization, 'dolor', 'dolor', []),
                    $nodes[] = $this->buildNode($owner, $organization, 'sit amet', 'sit-amet', [])
                ]),
                $nodes[] = $this->buildNode($owner, $organization, 'Consecutir', 'consecutir', [
                    $nodes[] = $this->buildNode($owner, $organization, 'Urlis med', 'urlis-med', []),
                ])
            ]
        );

        $manager->persist($tree);

        array_walk($nodes, function(DocumentNode $item) use($tree, $manager) {
            $manager->persist($item);
        });

        $manager->flush();
        $manager->clear();
    }

    /**
     * @param Channel        $integration
     * @param User           $owner
     * @param Organization   $organization
     * @param string         $name
     * @param string         $slug
     * @param DocumentNode[] $children
     *
     * @return TeamStorageNode
     */
    private function buildRoot(
        Channel $integration,
        User $owner,
        Organization $organization,
        string $name,
        string $slug,
        array $children
    ): TeamStorageNode {
        $tree = new TeamStorageNode();

        $tree->setOwner($owner);
        $tree->setOrganization($organization);

        $tree->setIntegration($integration);

        $tree->addName($this->buildFallback($name));
        $tree->addSlug($this->buildFallback($slug));

        foreach ($children as $child) {
            $tree->addNode($child);
            $child->moveTo($tree);
        }

        return $tree;
    }

    /**
     * @param User           $owner
     * @param Organization   $organization
     * @param string         $name
     * @param string         $slug
     * @param DocumentNode[] $children
     *
     * @return DocumentNode
     */
    private function buildNode(
        User $owner,
        Organization $organization,
        string $name,
        string $slug,
        array $children
    ): DocumentNode {
        $node = new DocumentNode();

        $node->setOwner($owner);
        $node->setOrganization($organization);

        $node->addName($this->buildFallback($name));
        $node->addSlug($this->buildFallback($slug));

        foreach ($children as $child) {
            $node->addNode($child);
            $child->moveTo($node);
        }

        return $node;
    }

    private function buildFallback(string $string): LocalizedFallbackValue
    {
        $node = new LocalizedFallbackValue();
        $node->setString($string);
        return $node;
    }
}
