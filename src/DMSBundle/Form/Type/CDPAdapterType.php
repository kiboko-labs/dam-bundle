<?php

namespace Kiboko\Bundle\DMSBundle\Form\Type;

use Kiboko\Bundle\DMSBundle\Entity\CDPStorage;
use Kiboko\Bundle\DMSBundle\Form\Listener\ConnectorSubscriber;
use Oro\Bundle\IntegrationBundle\Manager\TypesRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CDPAdapterType extends AbstractType
{
    /**
     * @var TypesRegistry
     */
    private $registry;

    /**
     * @param TypesRegistry $registry
     */
    public function __construct(TypesRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'url',
                TextType::class
            )
            ->add(
                'client',
                TextType::class
            )
            ->add(
                'secret',
                TextType::class
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CDPStorage::class,
        ]);
    }
}
