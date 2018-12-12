<?php

namespace Kiboko\Bundle\DAMBundle\Form\Type;

use Oro\Bundle\AttachmentBundle\Form\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UploadAssetWidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'file',
            TextType::class
        );
    }
}
