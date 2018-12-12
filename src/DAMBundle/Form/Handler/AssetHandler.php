<?php

namespace Kiboko\Bundle\DAMBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use Kiboko\Bundle\DAMBundle\Entity\Document;
use Kiboko\Bundle\DAMBundle\Model\DocumentInterface;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;
use Oro\Bundle\FormBundle\Form\Handler\RequestHandlerTrait;
use Symfony\Component\CssSelector\Parser\Handler\HashHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AssetHandler implements FormHandlerInterface
{
    use RequestHandlerTrait;

    /** @var FormInterface */
    protected $form;

    /** @var EntityManager */
    protected $em;

    /** @var DocumentNodeInterface */
    protected $node;

    /**
     * @param FormInterface $form
     * @param EntityManager $em
     * @param DocumentNodeInterface $node
     */
    public function __construct(
        FormInterface $form,
        EntityManager $em,
        DocumentNodeInterface $node
    ) {
        $this->form = $form;
        $this->em = $em;
        $this->node = $node;
    }

    /**
     * @param $data
     * @param FormInterface $form
     * @param Request $request
     * @return bool True on successful processing, false otherwise
     */
    public function process($data, FormInterface $form, Request $request)
    {
        $form->setData($data);

        if (!in_array($request->getMethod(), ['POST', 'PUT'], true)) {
            return false;
        }

        $this->form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return false;
        }

        $file = $form->getData();
    }

}
