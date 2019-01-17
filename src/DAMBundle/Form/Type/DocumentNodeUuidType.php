<?php

namespace Kiboko\Bundle\DAMBundle\Form\Type;

use DAMBundle\Form\DataTransformer\DocumentNodeUuidDataTransformer;
use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentNodeUuidType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder->create(
                'uuid',
                TextType::class,
                [
                    'label' => 'kiboko.dam.form.type.documentnodeuuid.fields.uuid.label',
                    'attr' => [
                        'readonly' => true,
                    ],
                ]
            )->addModelTransformer(new DocumentNodeUuidDataTransformer())
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
