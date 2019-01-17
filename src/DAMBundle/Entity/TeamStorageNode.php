<?php

namespace Kiboko\Bundle\DAMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\IntegrationBundle\Entity\Channel as Integration;

/**
 * @ORM\Entity(repositoryClass="Kiboko\Bundle\DAMBundle\Repository\TeamStorageNodeRepository")
 * @Config(
 *      routeName="kiboko_dam_root_browse",
 *      routeCreate="kiboko_dam_storage_create",
 *      routeUpdate="kiboko_dam_storage_update",
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
class TeamStorageNode extends DocumentNode
{
    /**
     * @var Integration
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\IntegrationBundle\Entity\Channel", cascade={"persist"})
     * @ORM\JoinColumn(name="integration_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $integration;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var File
     *
     * @ORM\OneToOne(targetEntity="Oro\Bundle\AttachmentBundle\Entity\File", cascade={"persist"})
     */
    private $thumbnail;

    /**
     * @return Integration
     */
    public function getIntegration(): ?Integration
    {
        return $this->integration;
    }

    /**
     * @param Integration $integration
     */
    public function setIntegration(Integration $integration): void
    {
        $this->integration = $integration;
    }

    /**
     * @return File
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param File $thumbnail
     */
    public function setThumbnail(File $thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }
}
