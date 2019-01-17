<?php

namespace Kiboko\Bundle\DAMBundle\Controller;

use Kiboko\Bundle\DAMBundle\Entity\Document;
use Kiboko\Bundle\DAMBundle\Form\Handler\DocumentHandler;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\UIBundle\Route\Router;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/document", service="kiboko_dam.controller.document")
 */
final class DocumentController extends Controller
{
    /**
     * @var DocumentHandler
     */
    private $handler;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var UpdateHandlerFacade
     */
    private $formUpdateHandler;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param FormInterface       $form
     * @param UpdateHandlerFacade $formUpdateHandler
     * @param TranslatorInterface $translator
     * @param DocumentHandler     $handler
     * @param Router              $router
     * @param Session             $session
     */
    public function __construct(
        FormInterface $form,
        UpdateHandlerFacade $formUpdateHandler,
        TranslatorInterface $translator,
        DocumentHandler $handler,
        Router $router,
        Session $session
    ) {
        $this->form = $form;
        $this->formUpdateHandler = $formUpdateHandler;
        $this->translator = $translator;
        $this->handler = $handler;
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @param DocumentNodeInterface $node
     *
     * @return array|Request
     *
     * @Route("/{node}/upload",
     *     name="kiboko_dam_document_upload",
     *     requirements={"node"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"},
     * )
     * @ParamConverter("node",
     *     class="KibokoDAMBundle:DocumentNode",
     *     options={
     *         "mapping": {"node": "uuid"},
     *         "map_method_signature" = true,
     *     }
     * )
     * @Acl(
     *      id="kiboko_dam_document_create",
     *      type="entity",
     *      class="KibokoDAMBundle:Document",
     *      permission="CREATE"
     * )
     * @Template("KibokoDAMBundle:Document:update.html.twig")
     */
    public function createAction(Request $request, DocumentNodeInterface $node)
    {
        return $this->updateWidget($request, $node, new Document(), $request->getUri());
    }

    /**
     * @param Request               $request
     * @param DocumentNodeInterface $node
     * @param Document              $document
     * @param string                $formAction
     * @param bool                  $update
     *
     * @return array
     */
    private function updateWidget(
        Request $request,
        DocumentNodeInterface $node,
        Document $document,
        string $formAction,
        bool $update = false
    ): array {
        $entity = $this->form->getData();
        $responseData = [
            'entity' => $entity,
            'saved'  => false,
            'update' => $update,
        ];

        if ($this->handler->process($document, $node)) {
            return array_merge(
                $responseData,
                [
                    'saved' => true,
                ]
            );
        }

        return array_merge(
            $responseData,
            [
                'form' => $this->form->createView(),
                'formAction' => $formAction,
            ]
        );
    }

    /**
     * @param Document $document
     * @return array|Response
     *
     * @Route("/{node}/view",
     *     name="kiboko_dam_document_view",
     *     requirements={"node"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"}
     * )
     * @ParamConverter("document",
     *     class="KibokoDAMBundle:Document",
     *     options={
     *         "mapping": {
     *             "node": "uuid",
     *         },
     *         "map_method_signature" = true,
     *     }
     * )
     * @Acl(
     *      id="kiboko_dam_document_view",
     *      type="entity",
     *      class="KibokoDAMBundle:DocumentDocument",
     *      permission="VIEW"
     * )
     * @Template()
     */
    public function viewAction(Document $document)
    {
        return [
            'entity' => $document,
        ];
    }

    /**
     * @param Document $document
     * @param Request $request
     * @return array|Response
     *
     * @Route("/{node}/update",
     *     name="kiboko_dam_document_update",
     *     requirements={"node"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"}
     * )
     * @ParamConverter("document",
     *     class="KibokoDAMBundle:Document",
     *     options={
     *         "mapping": {
     *             "node": "uuid",
     *         },
     *         "map_method_signature" = true,
     *     }
     * )
     * @Acl(
     *      id="kiboko_dam_document_view",
     *      type="entity",
     *      class="KibokoDAMBundle:DocumentDocument",
     *      permission="VIEW"
     * )
     * @Template()
     */
    public function updateAction(Document $document, Request $request)
    {
        return $this->update($document, $request);
    }

    protected function update(Document $document, Request $request)
    {
        return $this->formUpdateHandler->update(
            $document,
            $this->form,
            $this->translator->trans('kiboko.dam.views.document.update.save.label'),
            $request,
            null
        );
    }
}
