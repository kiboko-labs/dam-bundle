<?php

namespace Kiboko\Bundle\DAMBundle\Controller;

use Kiboko\Bundle\DAMBundle\Entity\TeamStorageNode;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
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
     * @return array|Request
     *
     * @Route("/",
     *     name="kiboko_dam_index",
     *     requirements={"uuid"="[\da-z]{8}-[\da-z]{4}-[\da-z]{4}-[\da-z]{4}-[\da-z]{12}"}
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
     * @Route("/browse/{slug}/", name="kiboko_dam_storage_browse")
     * @ParamConverter("node",
     *     class="KibokoDAMBundle:TeamStorageNode",
     *     options={
     *         "repository_method" = "findBySlug",
     *         "mapping": {"slug": "slug"},
     *         "map_method_signature" = true,
     *     }
     * )
     * @Template()
     */
    public function browseAction(DocumentNodeInterface $node)
    {
        return [
            'node' => $node
        ];
    }

    /**
     * @param Request $request
     *
     * @return array|Request
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
     * @return array|Request
     *
     * @Route("/{uuid}/update",
     *     name="kiboko_dam_storage_update",
     *     requirements={"uuid"="[\da-z]{8}-[\da-z]{4}-[\da-z]{4}-[\da-z]{4}-[\da-z]{12}"}
     * )
     * @ParamConverter("node",
     *     class="KibokoDAMBundle:TeamStorageNode",
     *     options={
     *         "mapping": {"uuid": "id"},
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
            $this->translator->trans('The Team Storage has been properly created'),
            $request
        );
    }
}
