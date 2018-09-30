<?php

namespace Kiboko\Bundle\DMSBundle\Controller;

use Kiboko\Bundle\DMSBundle\Entity\Document;
use Kiboko\Bundle\DMSBundle\Form\Handler\DocumentHandler;
use Kiboko\Bundle\DMSBundle\Model\DocumentNodeInterface;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\UIBundle\Route\Router;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\DataCollectorTranslator;

/**
 * @Route("/document", service="kiboko_dms.controller.document")
 */
final class DocumentController extends Controller
{
    /**
     * @var Form
     */
    private $form;

    /**
     * @var DocumentHandler
     */
    private $handler;

    /**
     * @var DataCollectorTranslator
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
     * @param Form                    $form
     * @param DocumentHandler         $handler
     * @param DataCollectorTranslator $translator
     * @param Router                  $router
     * @param Session                 $session
     */
    public function __construct(
        Form $form,
        DocumentHandler $handler,
        DataCollectorTranslator $translator,
        Router $router,
        Session $session
    ) {
        $this->form = $form;
        $this->handler = $handler;
        $this->translator = $translator;
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @param DocumentNodeInterface $node
     *
     * @return array|Request
     *
     * @Route("/{uuid}/upload", name="kiboko_dms_document_upload")
     * @ParamConverter("node",
     *     class="KibokoDMSBundle:DocumentNode",
     *     options={
     *         "mapping": {"uuid": "id"},
     *         "map_method_signature" = true,
     *     }
     * )
     * @Acl(
     *      id="kiboko_dms_document_create",
     *      type="entity",
     *      class="KibokoDMSBundle:Document",
     *      permission="CREATE"
     * )
     * @Template("KibokoDMSBundle:Document:update.html.twig")
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

    private function update(
        Request $request,
        DocumentNodeInterface $node,
        Document $document
    ): array {
        if ($this->handler->process($document, $node)) {
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('The file has been successfully uploaded.')
            );

            return $this->router->redirect($document);
        }

        return [
            'node' => $node,
            'document' => $document,
            'form' => $this->form->createView(),
        ];
    }
}
