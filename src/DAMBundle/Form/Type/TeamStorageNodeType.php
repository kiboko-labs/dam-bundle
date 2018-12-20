<?php

namespace Kiboko\Bundle\DAMBundle\Form\Type;

use Kiboko\Bundle\DAMBundle\Entity\TeamStorageNode;
use Oro\Bundle\IntegrationBundle\Form\Type\IntegrationSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamStorageNodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'integration',
                IntegrationSelectType::class,
                [
                    'label' => 'kiboko.dam.form.type.teamstoragenode.fields.integration.label',
                    'required' => true,
                    'allowed_types' => ['kiboko_dam']
                ]
            )
            ->remove('parent')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TeamStorageNode::class,
        ]);
    }

    public function getParent()
    {
        return DocumentNodeType::class;
    }
}
