<?php

namespace Kiboko\Bundle\DAMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kiboko\Bundle\DAMBundle\Model\MetaInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\LocaleBundle\Entity\Localization;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "document" = "Kiboko\Bundle\DAMBundle\Entity\DocumentMeta",
 *     "node" = "Kiboko\Bundle\DAMBundle\Entity\DocumentNodeMeta"
 * })
 * @ORM\Table(name="kiboko_dam_metadata")
 */
abstract class Meta implements MetaInterface
{
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
     * @var Localization|null
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\LocaleBundle\Entity\Localization")
     * @ORM\JoinColumn(name="localization_id", referencedColumnName="id", onDelete="CASCADE")
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "identity"=true
     *          }
     *      }
     * )
     */
    private $localization;

    /**
     * @var array
     *
     * @ORM\Column(name="raw", type="json")
     */
    private $raw;

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setLocalization(Localization $localization): void
    {
        $this->localization = $localization;
    }

    public function getLocalization(): ?Localization
    {
        return $this->localization;
    }

    /**
     * @return array
     */
    public function getRaw(): array
    {
        return $this->raw;
    }

    /**
     * @param array $raw
     */
    public function setRaw(array $raw): void
    {
        $this->raw = $raw;
    }
}
