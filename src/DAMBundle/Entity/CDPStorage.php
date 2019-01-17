<?php

namespace Kiboko\Bundle\DAMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class CDPStorage extends Transport
{
    /**
     * @var string
     *
     * @ORM\Column(name="kbk_dam_cdp_url", type="string", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private $url;

    /**
     * @var int
     *
     * @ORM\Column(name="kbk_dam_cdp_client", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $client;

    /**
     * @var int
     *
     * @ORM\Column(name="kbk_dam_cdp_secret", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $secret;

    public function __construct()
    {
        $this->lock = true;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getClient(): ?string
    {
        return $this->client;
    }

    /**
     * @param string $client
     */
    public function setClient(string $client): void
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    public function getSettingsBag()
    {
        return new ParameterBag([
            'url' => $this->url,
            'client' => $this->client,
            'secret' => $this->secret,
        ]);
    }
}
