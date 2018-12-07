<?php

namespace Kiboko\Bundle\DAMBundle\Controller;

use Kiboko\Bundle\DAMBundle\Entity\TeamStorageNode;
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
 * @Route(service="kiboko_dam.controller.team_storage")
 */
final class TeamStorageNodeController extends Controller
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
     * @Route("/",
     *     name="kiboko_dam_index",
     *     requirements={"uuid"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"}
     * )
     * @Acl(
     *      id="kiboko_dam_storage_view",
     *      type="entity",
     *      class="KibokoDAMBundle:TeamStorageNode",
     *      permission="VIEW"
     * )
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @param Request $request
     *
     * @return array|Response
     *
     * @Route("/create", name="kiboko_dam_storage_create")
     * @Acl(
     *      id="kiboko_dam_storage_create",
     *      type="entity",
     *      class="KibokoDAMBundle:TeamStorageNode",
     *      permission="CREATE"
     * )
     * @Template("KibokoDAMBundle:TeamStorageNode:update.html.twig")
     */
    public function createAction(Request $request)
    {
        return $this->update($request, new TeamStorageNode());
    }

    /**
     * @param Request               $request
     * @param DocumentNodeInterface $node
     *
     * @return array|Response
     *
     * @Route("/{uuid}/update",
     *     name="kiboko_dam_storage_update",
     *     requirements={"uuid"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"},
     * )
     * @ParamConverter("uuid",
     *     class="KibokoDAMBundle:TeamStorageNode",
     *     options={
     *         "mapping": {
     *             "uuid": "uuid",
     *         },
     *         "map_method_signature" = true,
     *     }
     * )
     * @Acl(
     *      id="kiboko_dam_storage_update",
     *      type="entity",
     *      class="KibokoDAMBundle:TeamStorageNode",
     *      permission="UPDATE"
     * )
     * @Template("KibokoDAMBundle:TeamStorageNode:update.html.twig")
     */
    public function updateAction(Request $request, DocumentNodeInterface $node)
    {
        return $this->update($request, $node);
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
            $this->translator->trans('kiboko.dam.teamstoragenode.message.success'),
            $request
        );
    }
}
