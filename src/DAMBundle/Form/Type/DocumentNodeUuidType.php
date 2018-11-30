<?php

namespace Kiboko\Bundle\DAMBundle\Form\Type;

use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentNodeUuidType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder->create(
                'id',
                TextType::class,
                [
                    'attr' => [
                        'readonly' => true,
                    ],
                ]
            )->addModelTransformer(new class implements DataTransformerInterface {
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
            })
        );

        $builder->remove('owner');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DocumentNode::class,
        ]);
    }
}
