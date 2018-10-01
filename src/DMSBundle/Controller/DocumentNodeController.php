<?php

namespace Kiboko\Bundle\DMSBundle\Controller;

use Kiboko\Bundle\DMSBundle\Entity\DocumentNode;
use Kiboko\Bundle\DMSBundle\Model\DocumentNodeInterface;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\DataCollectorTranslator;

/**
 * @Route("/node", service="kiboko_dms.controller.document_node")
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
     * @var DataCollectorTranslator
     */
    private $translator;

    /**
     * @param Form                    $form
     * @param UpdateHandlerFacade     $handler
     * @param DataCollectorTranslator $translator
     */
    public function __construct(
        Form $form,
        UpdateHandlerFacade $handler,
        DataCollectorTranslator $translator
    ) {
        $this->form = $form;
        $this->handler = $handler;
        $this->translator = $translator;
    }

    /**
     * @return array|Response
     *
     * @Route("/{uuid}/browse",
     *     name="kiboko_dms_node_browse",
     *     requirements={"uuid"="[\da-z]{8}-[\da-z]{4}-[\da-z]{4}-[\da-z]{4}-[\da-z]{12}"}
     * )
     * @ParamConverter("node",
     *     class="KibokoDMSBundle:DocumentNode",
     *     options={
     *         "mapping": {"uuid": "id"},
     *         "map_method_signature" = true,
     *     }
     * )
     * @Acl(
     *      id="kiboko_dms_node_view",
     *      type="entity",
     *      class="KibokoDMSBundle:DocumentNode",
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
        ];
    }

    /**
     * @param Request $request
     *
     * @return array|Response
     *
     * @Route("/{uuid}/create",
     *     name="kiboko_dms_node_create",
     *     requirements={"uuid"="[\da-z]{8}-[\da-z]{4}-[\da-z]{4}-[\da-z]{4}-[\da-z]{12}"}
     * )
     * @ParamConverter("parent",
     *     class="KibokoDMSBundle:DocumentNode",
     *     options={
     *         "mapping": {"uuid": "id"},
     *         "map_method_signature" = true,
     *     }
     * )
     * @Acl(
     *      id="kiboko_dms_node_create",
     *      type="entity",
     *      class="KibokoDMSBundle:DocumentNode",
     *      permission="CREATE"
     * )
     * @Template("KibokoDMSBundle:DocumentNode:update.html.twig")
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
     *     name="kiboko_dms_node_update",
     *     requirements={"uuid"="[\da-z]{8}-[\da-z]{4}-[\da-z]{4}-[\da-z]{4}-[\da-z]{12}"}
     * )
     * @ParamConverter("node",
     *     class="KibokoDMSBundle:DocumentNode",
     *     options={
     *         "mapping": {"uuid": "id"},
     *         "map_method_signature" = true,
     *     }
     * )
     * @Acl(
     *      id="kiboko_dms_node_edit",
     *      type="entity",
     *      class="KibokoDMSBundle:DocumentNode",
     *      permission="EDIT,SHARE"
     * )
     * @Template("KibokoDMSBundle:DocumentNode:update.html.twig")
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
     *     name="kiboko_dms_node_delete",
     *     requirements={"uuid"="[\da-z]{8}-[\da-z]{4}-[\da-z]{4}-[\da-z]{4}-[\da-z]{12}"}
     * )
     * @ParamConverter("parent",
     *     class="KibokoDMSBundle:DocumentNode",
     *     options={
     *         "mapping": {"uuid": "id"},
     *         "map_method_signature" = true,
     *     }
     * )
     * @Acl(
     *      id="kiboko_dms_node_create",
     *      type="entity",
     *      class="KibokoDMSBundle:DocumentNode",
     *      permission="CREATE"
     * )
     * @Template("KibokoDMSBundle:DocumentNode:update.html.twig")
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
