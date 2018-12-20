<?php

namespace Kiboko\Bundle\DAMBundle\Form\Type;

use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DocumentNodeType extends AbstractType
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
                    'label' => 'kiboko.dam.form.type.documentnode.fields.names.label'
                ]
            )
            ->add(
                'slugs',
                LocalizedFallbackValueCollectionType::class,
                [
                    'required' => true,
                    'entry_options' => ['constraints' => [new NotBlank()]],
                    'label' => 'kiboko.dam.form.type.documentnode.fields.slugs.label'
                ]
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
