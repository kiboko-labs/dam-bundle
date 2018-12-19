<?php

namespace Kiboko\Bundle\DAMBundle\Form\Type;

use Kiboko\Bundle\DAMBundle\Entity\Document;
use Oro\Bundle\AttachmentBundle\Form\Type\FileType;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'names',
                LocalizedFallbackValueCollectionType::class,
                [
                    'required' => true,
                    'entry_options' => ['constraints' => [new NotBlank()]],
                    'label' => 'kiboko.dam.form.type.document.fields.names.label'
                ]
            )
            ->add(
                'slugs',
                LocalizedFallbackValueCollectionType::class,
                [
                    'required' => true,
                    'entry_options' => ['constraints' => [new NotBlank()]],
                    'label' => 'kiboko.dam.form.type.document.fields.slugs.label'
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'checkEmptyFile' => true,
                    'required' => true,
                    'label' => 'kiboko.dam.form.type.document.fields.file.label'
                ]
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
