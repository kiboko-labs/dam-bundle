<?php

namespace Kiboko\Bundle\DMSBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kiboko\Bundle\DMSBundle\Model\AuthorizationInterface;
use Kiboko\Bundle\DMSBundle\Model\Behavior;
use Kiboko\Bundle\DMSBundle\Model\DocumentInterface;
use Kiboko\Bundle\DMSBundle\Model\DocumentNodeInterface;
use Kiboko\Bundle\DMSBundle\Model\MetaInterface;
use Kiboko\Bundle\DMSBundle\Model\Path;
use Kiboko\Bundle\DMSBundle\Model\PathInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityBundle\EntityProperty\UpdatedByAwareInterface;
use Oro\Bundle\EntityBundle\EntityProperty\UpdatedByAwareTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\LocaleBundle\Entity\Localization;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="Kiboko\Bundle\DMSBundle\Repository\DocumentNodeRepository")
 * @ORM\Table(name="kiboko_dms_node")
 * @Gedmo\Tree(type="nested")
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
     * @var UuidInterface
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var Collection|LocalizedFallbackValue[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue",
     *      cascade={"ALL"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinTable(
     *      name="kiboko_dms_node_name",
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
     *      orphanRemoval=true
     * )
     * @ORM\JoinTable(
     *      name="kiboko_dms_node_slug",
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
     *      targetEntity="Kiboko\Bundle\DMSBundle\Model\MetaInterface",
     *      cascade={"ALL"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinTable(
     *      name="kiboko_dms_node_metadata",
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
     *      targetEntity="Kiboko\Bundle\DMSBundle\Entity\DocumentNodeAuthorization",
     *      cascade={"ALL"},
     *      orphanRemoval=true,
     *      inversedBy="nodes",
     * )
     * @ORM\JoinTable(
     *      name="kiboko_dms_node_authorization",
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
     * @ORM\ManyToOne(targetEntity="Kiboko\Bundle\DMSBundle\Model\DocumentNodeInterface")
     * @ORM\JoinColumn(name="root_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @var DocumentNodeInterface
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Kiboko\Bundle\DMSBundle\Model\DocumentNodeInterface", inversedBy="nodes", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @var DocumentNodeInterface
     *
     * @ORM\OneToMany(targetEntity="Kiboko\Bundle\DMSBundle\Model\DocumentNodeInterface", mappedBy="parent")
     * @ORM\OrderBy({"left" = "ASC"})
     */
    private $nodes;

    /**
     * @var DocumentInterface
     *
     * @ORM\OneToMany(targetEntity="Kiboko\Bundle\DMSBundle\Model\DocumentInterface", mappedBy="node")
     */
    private $documents;

    public function __construct()
    {
        $this->names = new ArrayCollection();
        $this->slugs = new ArrayCollection();
        $this->metas = new ArrayCollection();
        $this->nodes = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->authorizations = new ArrayCollection();
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
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
