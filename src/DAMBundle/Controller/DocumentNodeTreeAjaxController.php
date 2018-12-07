<?php

namespace Kiboko\Bundle\DAMBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kiboko\Bundle\DAMBundle\Entity\DocumentNode;
use Kiboko\Bundle\DAMBundle\Model\DocumentNodeInterface;
use Oro\Bundle\NavigationBundle\Event\MenuUpdateChangeEvent;
use Oro\Bundle\NavigationBundle\Manager\MenuUpdateManager;
use Oro\Bundle\NavigationBundle\Provider\BuilderChainProvider;
use Oro\Bundle\NavigationBundle\Provider\MenuUpdateProvider;
use Oro\Bundle\OrganizationBundle\Provider\ScopeOrganizationCriteriaProvider;
use Oro\Bundle\ScopeBundle\Entity\Scope;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Provider\ScopeUserCriteriaProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/node/tree")
 */
class DocumentNodeTreeAjaxController extends Controller
{


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
        $em = $this->getDoctrine()->getManagerForClass(DocumentNode::class);

        $em->remove($node);
        $em->flush();

        return new JsonResponse('deleted',200);

    }


}
