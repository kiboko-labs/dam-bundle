<?php

namespace Kiboko\Bundle\DAMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class LocalStorage extends Transport
{
    /**
     * @var string
     *
     * @ORM\Column(name="kbk_dam_path", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $path;

    /**
     * @var int
     *
     * @ORM\Column(name="kbk_dam_local_lock", type="boolean", nullable=true)
     */
    private $lock;

    public function __construct()
    {
        $this->lock = true;
    }

    /**
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return bool
     */
    public function getLock(): bool
    {
        return $this->lock;
    }

    /**
     * @param bool $lock
     */
    public function setLock(bool $lock): void
    {
        $this->lock = $lock;
    }

    public function getSettingsBag()
    {
        return new ParameterBag([
            'path' => $this->path,
            'lock' => $this->lock,
        ]);
    }
}
