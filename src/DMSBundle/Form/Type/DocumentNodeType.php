<?php

namespace Kiboko\Bundle\DMSBundle\Form\Type;

use Kiboko\Bundle\DMSBundle\Entity\DocumentNode;
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
