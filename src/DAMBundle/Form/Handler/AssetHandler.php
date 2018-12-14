<?php

namespace Kiboko\Bundle\DAMBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use Kiboko\Bundle\DAMBundle\Entity\Document;
use Kiboko\Bundle\DAMBundle\Model\Behavior\MovableInterface;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;
use Oro\Bundle\FormBundle\Form\Handler\RequestHandlerTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return bool True on successful processing, false otherwise
     */
    public function process($data, FormInterface $form, Request $request)
    {

        if (!$data instanceof MovableInterface) {
            throw new \InvalidArgumentException('Argument data should be instance of Movable interface');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Document $document */
            $document = $form->getData();

            $this->node->addDocument($document);
            $document->moveTo($this->node);
            $this->em->persist($document);
            $this->em->persist($this->node);

            $this->em->flush();

            return true;
        }

        return false;
    }

}
