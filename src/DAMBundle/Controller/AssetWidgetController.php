<?php

namespace Kiboko\Bundle\DAMBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kiboko\Bundle\DAMBundle\Entity\Document;
use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Kiboko\Bundle\DAMBundle\Form\Handler\AssetHandler;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Kiboko\Bundle\DAMBundle\Provider\AssetProvider;
use Oro\Bundle\ActionBundle\Helper\ContextHelper;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * @Route("/asset-widget", service="kiboko_dam.controller.asset_widget")
 */
class AssetWidgetController extends Controller
{
    /** @var ContextHelper */
    private $helper;

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
     * @param ContextHelper $helper
     * @param EntityManager $em
     * @param UpdateHandlerFacade $formUpdateHandler
     * @param TranslatorInterface $translator
     * @param FormInterface $form
     */
    public function __construct(ContextHelper $helper, EntityManager $em, UpdateHandlerFacade $formUpdateHandler, TranslatorInterface $translator, FormInterface $form)
    {
        $this->helper = $helper;
        $this->em = $em;
        $this->formUpdateHandler = $formUpdateHandler;
        $this->translator = $translator;
        $this->form = $form;
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
     * @param DocumentNodeInterface $node
     * @param Request $request
     *
     * @return Response
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
    public function uploadAction(DocumentNodeInterface $node, Request $request)
    {
        $document = new Document();
        return $this->formUpdateHandler->update(
            $document,
            $this->form,
            $this->translator->trans('kiboko.document.save.ok'),
            $request,
            new AssetHandler($this->form, $this->em, $node),
            new AssetProvider($this->helper, $document, $this->form, $request)
        );
    }
}
