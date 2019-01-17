<?php

namespace DAMBundle\Form\DataTransformer;

use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Form\DataTransformerInterface;

class DocumentNodeUuidDataTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if ($value === null) {
            return null;
        }
        if (!$value instanceof UuidInterface) {
            throw new \UnexpectedValueException(strtr(
                'Expected a %expected% instance, but got a %actual%.',
                [
                    '%expected%' => UuidInterface::class,
                    '%actual%' => is_object($value) ? get_class($value) : gettype($value),
                ]
            ));
        }

        return (string) $value;
    }

    public function reverseTransform($value)
    {
        if ($value === null) {
            return null;
        }
        if (!is_string($value)) {
            throw new \UnexpectedValueException(strtr(
                'Expected a string, but got a %actual%.',
                [
                    '%actual%' => is_object($value) ? get_class($value) : gettype($value),
                ]
            ));
        }

        return (new UuidFactory())->fromString($value);
    }
}
