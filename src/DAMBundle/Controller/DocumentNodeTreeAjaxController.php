<?php

namespace Kiboko\Bundle\DAMBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/node/tree", service="kiboko_dam.controller.document_node_tree_ajax_controller")
 */
class DocumentNodeTreeAjaxController extends Controller
{

    /** @var EntityManager */
    private $em;

    /** @var LocalizationHelper */
    private $localizationHelper;

    /**
     * @param EntityManager $em
     * @param LocalizationHelper $localizationHelper
     */
    public function __construct(
        EntityManager $em,
        LocalizationHelper $localizationHelper
    ) {
        $this->em = $em;
        $this->localizationHelper = $localizationHelper;
    }


    /**
     * @Route("/delete/{uuid}",
     *     name="kiboko_dam_document_node_tree_ajax_delete",
     *     requirements={"uuid"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"},
     *     options={
     *         "expose"=true,
     *     },
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
     * @Method({"DELETE"})
     *
     * {@inheritdoc}
     */
    public function deleteAction(Request $request, DocumentNodeInterface $node)
    {
        try {
            $this->em->remove($node);
            $this->em->flush();
        }
        catch (ORMException $e) {
            return new JsonResponse($e->getMessage(),500);

        }

        return new JsonResponse('deleted',200);

    }

     /**
     * @Route("/rename/{uuid}",
     *     name="kiboko_dam_document_node_tree_ajax_rename",
     *     requirements={"uuid"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"},
     *     options={
     *         "expose"=true,
     *     },
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
     * @Method({"POST"})
     *
     * {@inheritdoc}
     */
        public function renameAction(Request $request, DocumentNodeInterface $node)
        {
            $newName = $request->get('newName');

            /** @var DocumentNode $oldNode */

            $oldName = $node->getLocaleName($this->localizationHelper);
            $oldName->setString($newName);

            $collection = new ArrayCollection();
            $collection->add($oldName);

            $node->setNames($collection);

            try {
                $this->em->persist($node);
                $this->em->flush();
            }
            catch (ORMException $e) {
                return new JsonResponse($e->getMessage(),500);
            }

            return new JsonResponse('renamed',200);
        }

    /**
     * @Route("/move/{uuid}",
     *     name="kiboko_dam_document_node_tree_ajax_move",
     *     requirements={"uuid"="[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}"},
     *     options={
     *         "expose"=true,
     *     },
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
     * @Method({"POST"})
     *
     * {@inheritdoc}
     */
    public function moveAction(Request $request, DocumentNodeInterface $node)
    {
        $newparent= $request->get('newParent');

        var_dump($newparent);die;
    }

}
