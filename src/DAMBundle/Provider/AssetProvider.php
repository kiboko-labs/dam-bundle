<?php

namespace Kiboko\Bundle\DAMBundle\Provider;

use Kiboko\Bundle\DAMBundle\Entity\Document;
use Oro\Bundle\ActionBundle\Helper\ContextHelper;
use Oro\Bundle\FormBundle\Provider\FormTemplateDataProviderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AssetProvider implements FormTemplateDataProviderInterface
{
    /** @var ContextHelper */
    private $helper;

    /** @var Document $entity */
    private $entity;

    /** @var FormInterface $form */
    private $form;

    /** @var Request $form */
    private $request;

    /**
     * @param ContextHelper $helper
     * @param Document $entity
     * @param FormInterface $form
     * @param Request $request
     */
    public function __construct(ContextHelper $helper, Document $entity, FormInterface $form, Request $request)
    {
        $this->helper = $helper;
        $this->entity = $entity;
        $this->form = $form;
        $this->request = $request;
    }


    public function getData($entity, FormInterface $form, Request $request)
    {
        return [
            'refreshGrid' => $this->helper->getActionData()->getRefreshGrid(),
        ];
    }
}