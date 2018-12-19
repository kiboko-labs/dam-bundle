<?php

namespace Kiboko\Bundle\DAMBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use Kiboko\Bundle\DAMBundle\Entity\Document;
use Kiboko\Bundle\DAMBundle\Model\Behavior\MovableInterface;
use Kiboko\Bundle\DAMBundle\Model\DocumentInterface;
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
        if (!$data instanceof DocumentInterface) {
            throw new \InvalidArgumentException(strtr(
                'Argument %argument% should be instance of %expected%, got %actual%.',
                [
                    '%argument%' => '$data',
                    '%expected%' => DocumentInterface::class,
                    '%actual%' => is_object($data) ? get_class($data) : gettype($data),
                ]
            ));
        }
        if (!$data instanceof MovableInterface) {
            throw new \InvalidArgumentException(strtr(
                'Argument %argument% should be instance of %expected%, got %actual%.',
                [
                    '%argument%' => '$data',
                    '%expected%' => MovableInterface::class,
                    '%actual%' => is_object($data) ? get_class($data) : gettype($data),
                ]
            ));
        }

        $form->setData($data);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return false;
        }

        /** @var DocumentInterface|MovableInterface $document */
        $document = $form->getData();

        $this->node->addDocument($document);

        $document->moveTo($this->node);

        try {
            $this->em->persist($document);
            $this->em->persist($this->node);

            $this->em->flush();
        } catch (ORMException|OptimisticLockException $e) {
            throw new \RuntimeException('Could not save the document to database, some error occurred while saving data.', null, $e);
        }

        return true;
    }

}
