<?php

namespace Kiboko\Bundle\DAMBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kiboko\Bundle\DAMBundle\Model\AuthorizationInterface;
use Kiboko\Bundle\DAMBundle\Model\Behavior;
use Kiboko\Bundle\DAMBundle\Model\DocumentInterface;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Kiboko\Bundle\DAMBundle\Model\MetaInterface;
use Kiboko\Bundle\DAMBundle\Model\Path;
use Kiboko\Bundle\DAMBundle\Model\PathInterface;
use Oro\Bundle\AttachmentBundle\Entity\File;
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
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="kiboko_dam_document")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
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
class Document implements DocumentInterface,
    Behavior\ThumbnailedInterface,
    Behavior\SluggableInterface,
    Behavior\MetadatableInterface,
    Behavior\AuthorizableInterface,
    Behavior\MovableInterface,
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
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
     * @ORM\JoinColumn(name="owner_user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $owner;

    /**
     * @var OrganizationInterface
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $organization;

    /**
     * @var Collection|LocalizedFallbackValue[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue",
     *      cascade={"ALL"},
     *      fetch="EXTRA_LAZY",
     * )
     * @ORM\JoinTable(
     *      name="kiboko_dam_document_name",
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
     *      name="kiboko_dam_document_slug",
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
     *      name="kiboko_dam_document_metadata",
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
     *      targetEntity="Kiboko\Bundle\DAMBundle\Entity\DocumentAuthorization",
     *      cascade={"ALL"},
     *      inversedBy="documents",
     *      fetch="EXTRA_LAZY",
     * )
     * @ORM\JoinTable(
     *      name="kiboko_dam_document_authorization",
     *      joinColumns={
     *          @ORM\JoinColumn(name="document_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="authorization_id", referencedColumnName="id", onDelete="CASCADE", unique=true)
     *      }
     * )
     */
    private $authorizations;

    /**
     * @var DocumentNodeInterface
     *
     * @ORM\ManyToOne(targetEntity="Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface", inversedBy="documents", cascade={"persist"})
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $node;

    /**
     * @var File
     *
     * @Assert\Valid()
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\AttachmentBundle\Entity\File", cascade={"persist"})
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     */
    private $file;

    /**
     * @var File
     *
     * @ORM\OneToOne(targetEntity="Oro\Bundle\AttachmentBundle\Entity\File", cascade={"persist"})
     */
    private $thumbnail;

    public function __construct()
    {
        $this->names = new ArrayCollection();
        $this->slugs = new ArrayCollection();
        $this->metas = new ArrayCollection();
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
     * @return UserInterface
     */
    public function getOwner(): ?UserInterface
    {
        return $this->owner;
    }

    /**
     * @param UserInterface $owner
     */
    public function setOwner(UserInterface $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @param OrganizationInterface $organization
     */
    public function setOrganization(OrganizationInterface $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * @return OrganizationInterface
     */
    public function getOrganization(): ?OrganizationInterface
    {
        return $this->organization;
    }

    /**
     * @param Collection|LocalizedFallbackValue[] $names
     */
    public function setNames(Collection $names): void
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
    public function setAuthorizations(Collection $authorizations): void
    {
        $this->authorizations = $authorizations->filter(
            function (DocumentAuthorization $authorization) {
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
        if (!$authorization instanceof DocumentAuthorization) {
            throw new \InvalidArgumentException(strtr(
                'Expected a %expected%, got a %actual%',
                [
                    '%expected%' => DocumentAuthorization::class,
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

    public function getMetas(): Collection
    {
        return $this->metas;
    }

    public function getLocaleMetas(?Localization $localization = null): Collection
    {
        return $this->metas->filter(
            function (MetaInterface $meta) use ($localization) {
                if ($localization === $meta->getLocalization()) {
                    return true;
                } else if (!$localization || !$meta->getLocalization()) {
                    return false;
                }

                return $localization->getId() === $meta->getLocalization()->getId();
            }
        );
    }

    public function setMetas(Collection $metas): void
    {
        $this->metas = $metas;
    }

    public function addMeta(MetaInterface $meta): void
    {
        $this->metas->add($meta);
    }

    public function removeMeta(MetaInterface $meta): void
    {
        $this->metas->removeElement($meta);
    }

    public function getNode(): ?DocumentNodeInterface
    {
        return $this->node;
    }

    public function setNode(DocumentNodeInterface $node): void
    {
        $this->node = $node;
    }

    public function getMimeType(): string
    {
        $this->file->getMimeType();
    }

    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setThumbnail(File $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function getThumbnail(): File
    {
        return $this->thumbnail;
    }

    public function getPath(): PathInterface
    {
        return new Path($this->file->getFilename());
    }

    public function moveTo(DocumentNodeInterface $node): void
    {
        $this->setNode($node);
    }
}
