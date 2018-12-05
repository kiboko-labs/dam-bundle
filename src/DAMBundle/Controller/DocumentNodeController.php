<?php

namespace Kiboko\Bundle\DAMBundle\Controller;

use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Kiboko\Bundle\DAMBundle\JsTree\DocumentNodeUpdateTreeHandler;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/node", service="kiboko_dam.controller.document_node")
 */
final class DocumentNodeController extends Controller
{
    /**
     * @var Form
     */
    private $form;

    /**
     * @var UpdateHandlerFacade
     */
    private $handler;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var DocumentNodeUpdateTreeHandler
     */
    private $treeHandler;

    /**
     * @param Form                          $form
     * @param UpdateHandlerFacade           $handler
     * @param TranslatorInterface           $translator
     * @param DocumentNodeUpdateTreeHandler $treeHandler
     */
    public function __construct(
        Form $form,
        UpdateHandlerFacade $handler,
        TranslatorInterface $translator,
        DocumentNodeUpdateTreeHandler $treeHandler
    ) {
        $this->form = $form;
        $this->handler = $handler;
        $this->translator = $translator;
        $this->treeHandler = $treeHandler;
    }

    /**
     * @return array|Response
     *
     * @Route("/{uuid}/browse",
     *     name="kiboko_dam_node_browse",
     *     requirements={"uuid"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"}
     * )
     * @ParamConverter("node",
     *     class="KibokoDAMBundle:DocumentNode",
     *     options={
     *         "mapping": {
     *             "uuid": "uuid",
     *         },
     *         "map_method_signature" = true,
     *     }
     * )
     * @Acl(
     *      id="kiboko_dam_node_view",
     *      type="entity",
     *      class="KibokoDAMBundle:DocumentNode",
     *      permission="VIEW"
     * )
     * @Template()
     */
    public function browseAction(DocumentNodeInterface $node)
    {
        $path = [];
        $parent = $node;
        while (($parent = $parent->getParent()) !== null) {
            $path[] = $parent;
        }

        return [
            'entity' => $node,
            'path' => $path,
            'tree' => $this->treeHandler->createTree($node, true),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array|Response
     *
     * @Route("/{uuid}/create",
     *     name="kiboko_dam_node_create",
     *     requirements={"uuid"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"}
     * )
     * @ParamConverter("parent",
     *     class="KibokoDAMBundle:DocumentNode",
     *     options={
     *         "mapping": {"uuid": "uuid"},
     *         "map_method_signature" = true,
     *     }
     * )
     * @Acl(
     *      id="kiboko_dam_node_create",
     *      type="entity",
     *      class="KibokoDAMBundle:DocumentNode",
     *      permission="CREATE"
     * )
     * @Template("KibokoDAMBundle:DocumentNode:update.html.twig")
     */
    public function createAction(Request $request, DocumentNodeInterface $parent)
    {
        $node = new DocumentNode();
        $node->setParent($parent);

        return $this->update($request, $node);
    }

    /**
     * @param Request               $request
     * @param DocumentNodeInterface $node
     *
     * @return array|Response
     *
     * @Route("/{uuid}/update",
     *     name="kiboko_dam_node_update",
     *     requirements={"uuid"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"}
     * )
     * @ParamConverter("node",
     *     class="KibokoDAMBundle:DocumentNode",
     *     options={
     *         "mapping": {"uuid": "uuid"},
     *         "map_method_signature" = true,
     *     }
     * )
     * @Acl(
     *      id="kiboko_dam_node_edit",
     *      type="entity",
     *      class="KibokoDAMBundle:DocumentNode",
     *      permission="EDIT,SHARE"
     * )
     * @Template("KibokoDAMBundle:DocumentNode:update.html.twig")
     */
    public function editAction(Request $request, DocumentNodeInterface $node)
    {
        return $this->update($request, $node);
    }

    /**
     * @param Request $request
     *
     * @return array|Response
     *
     * @Route("/{uuid}/delete",
     *     name="kiboko_dam_node_delete",
     *     requirements={"uuid"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"}
     * )
     * @ParamConverter("parent",
     *     class="KibokoDAMBundle:DocumentNode",
     *     options={
     *         "mapping": {"uuid": "uuid"},
     *         "map_method_signature" = true,
     *     }
     * )
     * @Acl(
     *      id="kiboko_dam_node_create",
     *      type="entity",
     *      class="KibokoDAMBundle:DocumentNode",
     *      permission="CREATE"
     * )
     * @Template("KibokoDAMBundle:DocumentNode:update.html.twig")
     */
    public function deleteAction(Request $request, DocumentNodeInterface $parent)
    {
        return new Response(null, 403);
    }

    /**
     * @param Request               $request
     * @param DocumentNodeInterface $node
     *
     * @return array|Response
     */
    private function update(Request $request, DocumentNodeInterface $node)
    {
        return $this->handler->update(
            $node,
            $this->form,
            $this->translator->trans('The Node has been properly created.'),
            $request
        );
    }
}
