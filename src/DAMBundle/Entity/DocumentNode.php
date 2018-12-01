<?php

namespace Kiboko\Bundle\DAMBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kiboko\Bundle\DAMBundle\Model\AuthorizationInterface;
use Kiboko\Bundle\DAMBundle\Model\Behavior;
use Kiboko\Bundle\DAMBundle\Model\DocumentInterface;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Kiboko\Bundle\DAMBundle\Model\MetaInterface;
use Kiboko\Bundle\DAMBundle\Model\Path;
use Kiboko\Bundle\DAMBundle\Model\PathInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityBundle\EntityProperty\UpdatedByAwareInterface;
use Oro\Bundle\EntityBundle\EntityProperty\UpdatedByAwareTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\LocaleBundle\Entity\Localization;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Oro\Bundle\UserBundle\Entity\UserInterface;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="Kiboko\Bundle\DAMBundle\Repository\DocumentNodeRepository")
 * @ORM\Table(name="kiboko_dam_node")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @Gedmo\Tree(type="nested")
 * @Config(
 *      routeName="kiboko_dam_index",
 *      routeCreate="kiboko_dam_node_create",
 *      routeUpdate="kiboko_dam_node_update",
 *      defaultValues={
 *          "entity"={
 *              "icon"="fa-file"
 *          },
 *          "ownership"={
 *              "owner_type"="USER",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="owner_id",
 *              "organization_field_name"="organization",
 *              "organization_column_name"="organization_id"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "category"="account_management"
 *          },
 *          "note"={
 *              "immutable"=true
 *          },
 *          "comment"={
 *              "immutable"=true
 *          },
 *          "activity"={
 *              "immutable"=true
 *          }
 *      }
 * )
 */
class DocumentNode implements DocumentNodeInterface,
    Behavior\SluggableInterface,
    Behavior\MovableInterface,
    Behavior\AuthorizableInterface,
    DatesAwareInterface,
    UpdatedByAwareInterface
{
    use DatesAwareTrait;
    use UpdatedByAwareTrait;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var UuidInterface
     *
     * @ORM\Column(type="uuid", unique=true)
     */
    private $uuid;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
     * @ORM\JoinColumn(name="owner_user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $owner;

    /**
     * @var OrganizationInterface
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $organization;

    /**
     * @var Collection|LocalizedFallbackValue[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue",
     *      cascade={"ALL"},
     *      fetch="EXTRA_LAZY",
     * )
     * @ORM\JoinTable(
     *      name="kiboko_dam_node_name",
     *      joinColumns={
     *          @ORM\JoinColumn(name="document_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="localized_value_id", referencedColumnName="id", onDelete="CASCADE", unique=true)
     *      }
     * )
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    private $names;

    /**
     * @var Collection|LocalizedFallbackValue[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue",
     *      cascade={"ALL"},
     *      fetch="EXTRA_LAZY",
     * )
     * @ORM\JoinTable(
     *      name="kiboko_dam_node_slug",
     *      joinColumns={
     *          @ORM\JoinColumn(name="document_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="localized_value_id", referencedColumnName="id", onDelete="CASCADE", unique=true)
     *      }
     * )
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    private $slugs;

    /**
     * @var Collection|MetaInterface[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Kiboko\Bundle\DAMBundle\Model\MetaInterface",
     *      cascade={"ALL"},
     *      fetch="EXTRA_LAZY",
     * )
     * @ORM\JoinTable(
     *      name="kiboko_dam_node_metadata",
     *      joinColumns={
     *          @ORM\JoinColumn(name="document_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="metadata_id", referencedColumnName="id", onDelete="CASCADE", unique=true)
     *      }
     * )
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    private $metas;

    /**
     * @var Collection|AuthorizationInterface[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Kiboko\Bundle\DAMBundle\Entity\DocumentNodeAuthorization",
     *      cascade={"ALL"},
     *      inversedBy="nodes",
     *      fetch="EXTRA_LAZY",
     * )
     * @ORM\JoinTable(
     *      name="kiboko_dam_node_authorization",
     *      joinColumns={
     *          @ORM\JoinColumn(name="node_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="authorization_id", referencedColumnName="id", onDelete="CASCADE", unique=true)
     *      }
     * )
     */
    private $authorizations;

    /**
     * @var int
     *
     * @Gedmo\TreeLeft
     * @ORM\Column(name="tree_left", type="integer")
     */
    private $left;

    /**
     * @var int
     *
     * @Gedmo\TreeRight
     * @ORM\Column(name="tree_right", type="integer")
     */
    private $right;

    /**
     * @var int
     *
     * @Gedmo\TreeLevel
     * @ORM\Column(name="tree_level", type="integer")
     */
    private $level;

    /**
     * @var DocumentNodeInterface
     *
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface")
     * @ORM\JoinColumn(name="root_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @var DocumentNodeInterface
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface", inversedBy="nodes", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @var DocumentNodeInterface
     *
     * @ORM\OneToMany(targetEntity="Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface", mappedBy="parent", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"left" = "ASC"})
     */
    private $nodes;

    /**
     * @var DocumentInterface
     *
     * @ORM\OneToMany(targetEntity="Kiboko\Bundle\DAMBundle\Model\DocumentInterface", mappedBy="node", fetch="EXTRA_LAZY")
     */
    private $documents;

    public function __construct()
    {
        $this->uuid = (new UuidFactory())->uuid4();
        $this->names = new ArrayCollection();
        $this->slugs = new ArrayCollection();
        $this->metas = new ArrayCollection();
        $this->nodes = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->authorizations = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUuid(): ?UuidInterface
    {
        return $this->uuid;
    }

    public function setUuid(UuidInterface $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return UserInterface
     */
    public function getOwner(): ?UserInterface
    {
        return $this->owner;
    }

    public function setOwner(UserInterface $owner): void
    {
        $this->owner = $owner;
    }

    public function setOrganization(OrganizationInterface $organization): void
    {
        $this->organization = $organization;
    }

    public function getOrganization(): ?OrganizationInterface
    {
        return $this->organization;
    }

    /**
     * @param Collection|LocalizedFallbackValue[] $names
     */
    public function setNames(Collection $names)
    {
        $this->names = $names;
    }

    public function getLocaleName(LocalizationHelper $helper, ?Localization $localization = null): ?LocalizedFallbackValue
    {
        return $helper->getLocalizedValue($this->names, $localization);
    }

    /**
     * @return Collection|LocalizedFallbackValue[]
     */
    public function getNames(): Collection
    {
        return $this->names;
    }

    public function addName(LocalizedFallbackValue $name): void
    {
        $this->names->add($name);
    }

    public function removeName(LocalizedFallbackValue $name): void
    {
        $this->names->removeElement($name);
    }

    /**
     * @param Collection|AuthorizationInterface[] $authorizations
     */
    public function setAuthorizations(Collection $authorizations)
    {
        $this->authorizations = $authorizations->filter(
            function (DocumentNodeAuthorization $authorization) {
                return true;
            }
        );
    }

    /**
     * @return Collection|AuthorizationInterface[]
     */
    public function getAuthorizations(): Collection
    {
        return $this->authorizations;
    }

    public function addAuthorization(AuthorizationInterface $authorization): void
    {
        if (!$authorization instanceof DocumentAuthorization) {
            throw new \InvalidArgumentException(strtr(
                'Expected a %expected%, got a %actual%',
                [
                    '%expected%' => DocumentAuthorization::class,
                    '%actual%' => get_class($authorization),
                ]
            ));
        }

        $this->authorizations->add($authorization);
    }

    public function removeAuthorization(AuthorizationInterface $authorization): void
    {
        if (!$authorization instanceof DocumentNodeAuthorization) {
            throw new \InvalidArgumentException(strtr(
                'Expected a %expected%, got a %actual%',
                [
                    '%expected%' => DocumentNodeAuthorization::class,
                    '%actual%' => get_class($authorization),
                ]
            ));
        }

        $this->authorizations->removeElement($authorization);
    }

    public function getSlugs(): Collection
    {
        return $this->slugs;
    }

    public function getLocaleSlug(LocalizationHelper $helper, ?Localization $localization = null): ?LocalizedFallbackValue
    {
        return $helper->getLocalizedValue($this->slugs, $localization);
    }

    public function setSlugs(Collection $slugs): void
    {
        $this->slugs = $slugs;
    }

    public function addSlug(LocalizedFallbackValue $slug): void
    {
        $this->slugs->add($slug);
    }

    public function removeSlug(LocalizedFallbackValue $slug): void
    {
        $this->slugs->removeElement($slug);
    }

    public function getParent(): ?DocumentNodeInterface
    {
        return $this->parent;
    }

    public function setParent(DocumentNodeInterface $parent): void
    {
        $this->parent = $parent;
    }

    public function getNodes(): Collection
    {
        return $this->nodes;
    }

    public function addNode(DocumentNodeInterface $node): void
    {
        $this->nodes->add($node);
    }

    public function removeNode(DocumentNodeInterface $node): void
    {
        $this->nodes->removeElement($node);
    }

    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(DocumentInterface $documents): void
    {
        $this->documents->add($documents);
    }

    public function removeDocument(DocumentInterface $documents): void
    {
        $this->documents->removeElement($documents);
    }

    public function getPath(?Localization $localization = null): PathInterface
    {
        if ($this->parent === null) {
            return new Path(null);
        }

        return $this->parent->getPath()->add($this->slugs);
    }

    public function moveTo(DocumentNodeInterface $node): void
    {
        $this->setParent($node);
    }
}
