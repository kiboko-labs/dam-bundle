<?php

namespace Kiboko\Bundle\DAMBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Kiboko\Bundle\DAMBundle\Form\Handler\AssetHandler;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Ramsey\Uuid\UuidInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * @Route("/asset-widget", service="kiboko_dam.controller.asset_widget")
 */
class AssetWidgetController extends Controller
{
    /** @var EntityManager $em */
    private $em;

    /**
     * @var UpdateHandlerFacade
     */
    private $formUpdateHandler;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * ComposeController constructor.
     *
     * @param UpdateHandlerFacade $formUpdateHandler
     * @param TranslatorInterface $translator
     * @param FormInterface $form
     * @param EntityManager $em
     */
    public function __construct(
        UpdateHandlerFacade $formUpdateHandler,
        TranslatorInterface $translator,
        FormInterface $form, EntityManager $em
    ) {
        $this->formUpdateHandler = $formUpdateHandler;
        $this->translator = $translator;
        $this->form = $form;
        $this->em = $em;

    }

    /**
     * @param Request               $request
     * @param DocumentNode $node
     *
     * @Route("/{uuid}",
     *     name="kiboko_dam_upload_asset_widget",
     *     requirements={"uuid"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"},
     *
     * )
     *
     * @ParamConverter("node",
     *     class="KibokoDAMBundle:DocumentNode",
     *     options={
     *         "mapping": {"uuid": "uuid"},
     *         "map_method_signature" = true,
     *     }
     * )
     *
     * @Template
     *
     * @return array
     */
    public function widgetAction(Request $request, DocumentNodeInterface $node)
    {

        return [
            'form' => $this->form->createView(),
            'formAction' => $this->generateUrl(
                'kiboko_dam_upload_asset',
                [
                    'uuid' => $node->getUuid()->toString(),
                ]

            )
        ];
    }

    /**
     * @param DocumentNode $node
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @Route("/{uuid}/upload",
     *     name="kiboko_dam_upload_asset",
     *     requirements={"uuid"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"},
     *
     * )
     * @Method({"POST", "PUT"})
     * @ParamConverter("node",
     *     class="KibokoDAMBundle:DocumentNode",
     *     options={
     *         "mapping": {"uuid": "uuid"},
     *         "map_method_signature" = true,
     *     }
     * )
     */
    public function uploadAction(DocumentNode $node, Request $request)
    {

        $var = $this->formUpdateHandler->update(
            $node,
            $this->form,
            $this->translator->trans('kiboko.project.ticket.add'),
            $request,
            new AssetHandler(
                $this->form,
                $this->em,
                $node
            )
        );


    }
}
