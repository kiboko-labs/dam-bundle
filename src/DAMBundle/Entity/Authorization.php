<?php

namespace Kiboko\Bundle\DAMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kiboko\Bundle\DAMBundle\Model\AuthorizationInterface;
use Kiboko\Bundle\DAMBundle\Model\Behavior\IdentifiableInterface;
use Ramsey\Uuid\UuidFactory;
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
abstract class Authorization implements AuthorizationInterface, IdentifiableInterface
{
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
     * @var string
     *
     * @ORM\Column(name="authorizations", type="array")
     */
    private $rawAuthorizations;

    /**
     * @var DocumentActionInterface[]
     */
    private $authorizations;

    /**
     * Authorization constructor.
     */
    public function __construct()
    {
        $this->uuid = (new UuidFactory())->uuid4();
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

    public function setUuid(UuidInterface $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }
}
