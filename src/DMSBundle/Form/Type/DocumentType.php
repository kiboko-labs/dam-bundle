<?php

namespace Kiboko\Bundle\DMSBundle\Form\Type;

use Kiboko\Bundle\DMSBundle\Entity\Document;
use Oro\Bundle\AttachmentBundle\Form\Type\FileType;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentType extends AbstractType
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
            ->add(
                'file',
                FileType::class
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
