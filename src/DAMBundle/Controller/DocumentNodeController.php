<?php

namespace Kiboko\Bundle\DAMBundle\Controller;

use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Kiboko\Bundle\DAMBundle\Entity\TeamStorageNode;
use Kiboko\Bundle\DAMBundle\JsTree\DocumentNodeUpdateTreeHandler;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Kiboko\Bundle\DAMBundle\Provider\NodeProvider;
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
     * @param TeamStorageNode $root
     * @param DocumentNode $node
     *
     * @return array|Response
     * @Route("/{root}/browse/{node}",
     *     name="kiboko_dam_node_browse",
     *     requirements={
     *         "root"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}",
     *         "node"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"
     *     }
     * )
     * @Route("/{root}/browse",
     *     name="kiboko_dam_root_browse",
     *     requirements={
     *         "root"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}",
     *     }
     * )
     * @ParamConverter("root",
     *     class="KibokoDAMBundle:TeamStorageNode",
     *     options={
     *         "mapping": {
     *             "root": "uuid",
     *         },
     *         "map_method_signature" = true,
     *     }
     * )
     * @ParamConverter("node",
     *     class="KibokoDAMBundle:DocumentNode",
     *     options={
     *         "mapping": {
     *             "node": "uuid",
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
     * @Template("@KibokoDAM/DocumentNode/browse.html.twig")
     */
    public function browseAction(TeamStorageNode $root, ?DocumentNode $node = null)
    {
        $path = [];
        $parent = $root;
        while (($parent = $parent->getParent()) !== null) {
            $path[] = $parent;
        }

        return [
            'teamstorage' => $root,
            'node' => $node,
            'path' => $path,
            'tree' => $this->treeHandler->createTree($root, $node),
        ];
    }

    /**
     * @param Request $request
     *
     * @param DocumentNodeInterface $parent
     * @param DocumentNodeInterface $root
     *
     * @return array|Response
     *
     * @Route("/{root}/create/{parent}",
     *     name="kiboko_dam_node_create",
     *     requirements={
     *         "root"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}",
     *         "parent"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"
     *     },
     *     options={
     *         "expose"=true,
     *     }
     * )
     * @Route("/{root}/create",
     *     name="kiboko_dam_root_create",
     *     requirements={
     *         "root"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}",
     *     },
     *     options={
     *         "expose"=true,
     *     }
     * )
     * @ParamConverter("root",
     *     class="KibokoDAMBundle:TeamStorageNode",
     *     options={
     *         "mapping": {
     *             "root": "uuid"
     *         },
     *         "map_method_signature" = true,
     *     }
     * )
     * @ParamConverter("parent",
     *     class="KibokoDAMBundle:DocumentNode",
     *     options={
     *         "mapping": {
     *             "parent": "uuid"
     *         },
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
    public function createAction(
        Request $request,
        DocumentNodeInterface $root,
        ?DocumentNodeInterface $parent = null
    ) {
        $node = new DocumentNode($root);
        $node->setParent($parent ?? $root);

        return $this->update($request, $node,$root);
    }

    /**
     * @param Request $request
     * @param DocumentNodeInterface $node
     * @param DocumentNodeInterface|null $root
     *
     * @return array|Response
     *
     * @Route("/{root}/update/{node}",
     *     name="kiboko_dam_node_update",
     *     requirements={
     *         "root"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}",
     *         "node"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}",
     *     }
     * )
     * @ParamConverter("node",
     *     class="KibokoDAMBundle:DocumentNode",
     *     options={
     *         "mapping": {
     *             "node": "uuid"
     *         },
     *         "map_method_signature" = true,
     *     }
     * )
     * @ParamConverter("root",
     *     class="KibokoDAMBundle:TeamStorageNode",
     *     options={
     *         "mapping": {
     *             "root": "uuid",
     *         },
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
    public function editAction(Request $request, DocumentNodeInterface $node, DocumentNodeInterface $root = null)
    {
        return $this->update($request, $node, $root);
    }

    /**
     * @param Request $request
     * @param DocumentNodeInterface $root
     * @param DocumentNodeInterface $node
     *
     * @return array|Response
     */
    private function update(Request $request, DocumentNodeInterface $node, DocumentNodeInterface $root = null)
    {
        return $this->handler->update(
            $node,
            $this->form,
            $this->translator->trans('kiboko.dam.documentnode.updated.label'),
            $request,
            null,
            new NodeProvider($root, $node, $this->form, $request)
        );
    }
}
