<?php

namespace Kiboko\Bundle\DMSBundle\Model;

class Path implements PathInterface
{
    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string[]
     */
    private $paths;

    /**
     * @param string   $scheme
     * @param string[] $paths
     */
    public function __construct(?string $scheme = null, string ...$paths)
    {
        $this->scheme = $scheme;
        $this->paths = $paths;
    }

    public static function fromString(string $uri): self
    {
        if (($position = strpos($uri, '://')) === null) {
            return new self(null, explode('/', $uri));
        }

        return new self(
            substr($uri, 0, $position),
            explode('/', substr($uri, $position + 4))
        );
    }

    public function add(string ...$paths): void
    {
        $this->paths = array_merge(
            $this->paths,
            $paths
        );
    }

    public function __toString()
    {
        if ($this->scheme === null) {
            return sprintf('/%s', $this->scheme, implode('/', $this->paths));
        }

        return sprintf('%s://%s', $this->scheme, implode('/', $this->paths));
    }
}
