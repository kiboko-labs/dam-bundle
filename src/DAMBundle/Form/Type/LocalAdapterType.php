<?php

namespace Kiboko\Bundle\DAMBundle\Form\Type;

use Kiboko\Bundle\DAMBundle\Entity\LocalStorage;
use Kiboko\Bundle\DAMBundle\Form\Listener\ConnectorSubscriber;
use Oro\Bundle\IntegrationBundle\Manager\TypesRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalAdapterType extends AbstractType
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
                'path',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'kiboko.dam.form.type.localadapter.fields.path.label'
                ]
            )
            ->add(
                'lock',
                CheckboxType::class,
                [
                    'required' => true,
                    'label' => 'kiboko.dam.form.type.localadapter.fields.lock.label'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocalStorage::class,
        ]);
    }
}
