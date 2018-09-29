<?php

namespace Kiboko\Bundle\DMSBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kiboko\Bundle\DMSBundle\Model\AuthorizationInterface;
use Kiboko\Bundle\DMSBundle\Model\Behavior;
use Kiboko\Bundle\DMSBundle\Model\DocumentInterface;
use Kiboko\Bundle\DMSBundle\Model\DocumentNodeInterface;
use Kiboko\Bundle\DMSBundle\Model\MetaInterface;
use Kiboko\Bundle\DMSBundle\Model\Path;
use Kiboko\Bundle\DMSBundle\Model\PathInterface;
use Oro\Bundle\AttachmentBundle\Entity\File;
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
 * @ORM\Entity
 * @ORM\Table(name="kiboko_dms_document")
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
     * @var Collection|LocalizedFallbackValue[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue",
     *      cascade={"ALL"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinTable(
     *      name="kiboko_dms_document_name",
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
     *      name="kiboko_dms_document_slug",
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
     *      name="kiboko_dms_document_metadata",
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
     *      targetEntity="Kiboko\Bundle\DMSBundle\Entity\DocumentAuthorization",
     *      cascade={"ALL"},
     *      orphanRemoval=true,
     *      inversedBy="nodes",
     * )
     * @ORM\JoinTable(
     *      name="kiboko_dms_document_authorization",
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
     * @ORM\ManyToOne(targetEntity="Kiboko\Bundle\DMSBundle\Model\DocumentNodeInterface", inversedBy="documents", cascade={"persist"})
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $node;

    /**
     * @var File
     *
     * @ORM\OneToOne(targetEntity="Oro\Bundle\AttachmentBundle\Entity\File", cascade={"persist"})
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

    public function getId(): UuidInterface
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

    public function getLocaleName(LocalizationHelper $helper, ?Localization $localization = null): LocalizedFallbackValue
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

    public function getLocaleSlug(LocalizationHelper $helper, ?Localization $localization = null): LocalizedFallbackValue
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

    public function getNode(): DocumentNodeInterface
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

    public function getFile(): File
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
