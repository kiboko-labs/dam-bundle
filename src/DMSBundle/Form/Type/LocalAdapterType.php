<?php

namespace Kiboko\Bundle\DMSBundle\Form\Type;

use Kiboko\Bundle\DMSBundle\Entity\LocalStorage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalAdapterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'path',
                TextType::class
            )
            ->add(
                'lock',
                CheckboxType::class
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocalStorage::class,
        ]);
    }
}
