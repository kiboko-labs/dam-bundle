<?php

namespace Kiboko\Bundle\DAMBundle\Form\Type;

use Kiboko\Bundle\DAMBundle\Entity\CDPStorage;
use Kiboko\Bundle\DAMBundle\Form\Listener\ConnectorSubscriber;
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
                TextType::class,
                [
                    'required' => true,
                    'label' => 'kiboko.dam.form.type.cdpadapter.fields.url.label'
                ]
            )
            ->add(
                'client',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'kiboko.dam.form.type.cdpadapter.fields.client.label'
                ]
            )
            ->add(
                'secret',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'kiboko.dam.form.type.cdpadapter.fields.secret.label'
                ]
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
