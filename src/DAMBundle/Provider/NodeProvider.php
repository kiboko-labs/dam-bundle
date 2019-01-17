<?php


namespace Kiboko\Bundle\DAMBundle\Provider;


use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\FormBundle\Provider\FormTemplateDataProviderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class NodeProvider implements FormTemplateDataProviderInterface
{

    /** @var DocumentNodeInterface */
    private $root;

    /** @var DocumentNodeInterface */
    private $node;

    /** @var FormInterface $form */
    private $form;

    /** @var Request $form */
    private $request;

    /**
     * NodeProvider constructor.
     * @param DocumentNodeInterface $root
     * @param DocumentNodeInterface $node
     * @param FormInterface $form
     * @param Request $request
     */
    public function __construct(DocumentNodeInterface $root,DocumentNodeInterface $node,FormInterface $form, Request $request)
    {

        $this->root = $root;
        $this->node = $node;
        $this->form = $form;
        $this->request = $request;
    }

    /**
     * @param object $entity
     * @param FormInterface $form
     * @param Request $request
     * @return array
     */
    public function getData($entity, FormInterface $form, Request $request)
    {
        return [
            'form' => $this->form->createView(),
            'root' => $this->root,
        ];
    }
}