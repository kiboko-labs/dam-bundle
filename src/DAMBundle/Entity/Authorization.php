<?php

namespace Kiboko\Bundle\DAMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kiboko\Bundle\DAMBundle\Model\AuthorizationInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "document" = "Kiboko\Bundle\DAMBundle\Entity\DocumentAuthorization",
 *     "node" = "Kiboko\Bundle\DAMBundle\Entity\DocumentNodeAuthorization"
 * })
 * @ORM\Table(name="kiboko_dam_authorization")
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
