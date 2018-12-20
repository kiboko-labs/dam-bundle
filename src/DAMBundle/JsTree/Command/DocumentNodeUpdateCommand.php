<?php

namespace Kiboko\Bundle\DAMBundle\JsTree\Command;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Kiboko\Bundle\DAMBundle\Model\Behavior\SluggableInterface;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\LocaleBundle\Entity\Repository\LocalizationRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DocumentNodeUpdateCommand implements CommandInterface
{
    /** @var DocumentNodeInterface */
    private $node;

    /** @var LocalizationRepository */
    private $localizationRepository;

    /** @var array */
    private $updates;

    /**
     * @param DocumentNodeInterface  $node
     * @param LocalizationRepository $localizationRepository
     * @param array                  $updates
     */
    public function __construct(
        DocumentNodeInterface $node,
        LocalizationRepository $localizationRepository,
        array $updates
    ) {
        $this->node = $node;
        $this->localizationRepository = $localizationRepository;
        $this->updates = $updates;
    }

    public function execute(
        ObjectManager $em,
        ValidatorInterface $validator
    ): DocumentNodeInterface {
        foreach ($this->updates['name'] as $languageCode => $name) {
            $this->setLocalizedValues($this->node->getNames(), $name, $languageCode);
        }

        if ($this->node instanceof SluggableInterface) {
            foreach ($this->updates['slug'] as $languageCode => $name) {
                $this->setLocalizedValues($this->node->getSlugs(), $name, $languageCode);
            }
        }

        if (!$validator->validate($this->node)) {
            throw new \RuntimeException();
        }

        $em->persist($this->node);

        return $this->node;
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
        $matchingValues = $locales->filter(function (LocalizedFallbackValue $value) use($languageCode) {
            return $value->getLocalization()->getLanguageCode() === $languageCode;
        });

        if ($matchingValues->count() > 0) {
            $matchingValues->map(function (LocalizedFallbackValue $value) use($name) {
                $value->setString($name);
            });

            return;
        }

        $value = new LocalizedFallbackValue();
        $value->setString($name);
        $value->setLocalization(
            $this->localizationRepository
                ->findOneBy(['languageCode' => $languageCode])
        );

        $locales->add($value);
    }
}
