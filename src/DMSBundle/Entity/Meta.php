<?php

namespace Kiboko\Bundle\DMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kiboko\Bundle\DMSBundle\Model\MetaInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\LocaleBundle\Entity\Localization;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\MappedSuperclass
 */
class Meta implements MetaInterface
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
}
