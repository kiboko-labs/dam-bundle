<?php

namespace Kiboko\Bundle\DAMBundle\JsTree\Command;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\LocaleBundle\Entity\Repository\LocalizationRepository;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DocumentNodeCreateCommand implements CommandInterface
{
    /** @var DocumentNode */
    private $parent;

    /** @var LocalizationRepository */
    private $localizationRepository;

    /** @var array */
    private $updates;

    /**
     * @param DocumentNode  $parent
     * @param LocalizationRepository $localizationRepository
     * @param array                  $updates
     */
    public function __construct(
        DocumentNode $parent,
        LocalizationRepository $localizationRepository,
        array $updates
    ) {
        $this->parent = $parent;
        $this->localizationRepository = $localizationRepository;
        $this->updates = $updates;
    }

    public function execute(
        ObjectManager $em,
        ValidatorInterface $validator
    ): DocumentNodeInterface {
        $node = new DocumentNode();

        try {
            $node->setUuid(Uuid::uuid4());
        } catch (UnsatisfiedDependencyException|\InvalidArgumentException $e) {
            throw new \RuntimeException('Could not generate UUID due to missing preconditions.', null, $e);
        } catch (\Exception $e) {
            throw new \RuntimeException('Could not generate UUID.', null, $e);
        }

        $node->setParent($this->parent);
        $this->parent->addNode($node);

        $node->setOwner($this->parent->getOwner());
        $node->setOrganization($this->parent->getOrganization());

        foreach ($this->updates['name'] as $languageCode => $name) {
            $this->setLocalizedValues($node->getNames(), $name, $languageCode);
        }

        foreach ($this->updates['slug'] as $languageCode => $name) {
            $this->setLocalizedValues($node->getSlugs(), $name, $languageCode);
        }

        if (!$validator->validate($node)) {
            throw new \RuntimeException();
        }

        $em->persist($node);

        return $node;
    }

    /**
     * @param LocalizedFallbackValue[]|Collection $locales
     * @param string                              $name
     * @param string                              $languageCode
     *
     * @return void
     */
    private function setLocalizedValues(Collection $locales, string $name, string $languageCode): void
    {
        $value = new LocalizedFallbackValue();
        $value->setString($name);
        $value->setLocalization(
            $this->localizationRepository
                ->findOneBy(['languageCode' => $languageCode])
        );

        $locales->add($value);
    }
}
