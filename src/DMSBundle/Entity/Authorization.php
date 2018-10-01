<?php

namespace Kiboko\Bundle\DMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kiboko\Bundle\DMSBundle\Model\AuthorizationInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "document" = "Kiboko\Bundle\DMSBundle\Entity\DocumentAuthorization",
 *     "node" = "Kiboko\Bundle\DMSBundle\Entity\DocumentNodeAuthorization"
 * })
 * @ORM\Table(name="kiboko_dms_authorization")
 */
abstract class Authorization implements AuthorizationInterface
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
     * @var string
     *
     * @ORM\Column(name="authorizations", type="array")
     */
    private $rawAuthorizations;

    /**
     * @var DocumentActionInterface[]
     */
    private $authorizations;

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
