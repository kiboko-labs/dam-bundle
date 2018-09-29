<?php

namespace Kiboko\Bundle\DMSBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use Kiboko\Bundle\DMSBundle\Entity\Document;
use Kiboko\Bundle\DMSBundle\Model\DocumentInterface;
use Kiboko\Bundle\DMSBundle\Model\DocumentNodeInterface;
use Oro\Bundle\FormBundle\Form\Handler\RequestHandlerTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DocumentHandler
{
    use RequestHandlerTrait;

    /** @var FormInterface */
    protected $form;

    /** @var RequestStack */
    protected $requestStack;

    /** @var EntityManager */
    protected $manager;

    /**
     * @param FormInterface $form
     * @param RequestStack  $requestStack
     * @param EntityManager $manager
     */
    public function __construct(FormInterface $form, RequestStack $requestStack, EntityManager $manager)
    {
        $this->form = $form;
        $this->requestStack = $requestStack;
        $this->manager = $manager;
    }

    /**
     * @param Document $entity
     * @param DocumentNodeInterface $node
     *
     * @return bool True on successful processing, false otherwise
     */
    public function process(Document $entity, DocumentNodeInterface $node)
    {
        $entity->setNode($node);

        $this->form->setData($entity);

        $request = $this->requestStack->getCurrentRequest();
        if (in_array($request->getMethod(), ['POST', 'PUT'], true)) {
            $this->submitPostPutRequest($this->form, $request);
            if ($this->form->isValid()) {
                $this->onSuccess($entity, $node);
                return true;
            }
        }

        return false;
    }

    /**
     * @param DocumentInterface $entity
     * @param DocumentNodeInterface $node
     */
    protected function onSuccess(DocumentInterface $entity, DocumentNodeInterface $node)
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
