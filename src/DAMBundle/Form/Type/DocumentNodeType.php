<?php

namespace Kiboko\Bundle\DAMBundle\Form\Type;

use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentNodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'parent',
                DocumentNodeUuidType::class
            )
            ->add(
                'names',
                LocalizedFallbackValueCollectionType::class
            )
            ->add(
                'slugs',
                LocalizedFallbackValueCollectionType::class
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DocumentNode::class,
        ]);
    }
}
