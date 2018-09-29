<?php

namespace Kiboko\Bundle\DMSBundle\Controller;

use Kiboko\Bundle\DMSBundle\Entity\DocumentNode;
use Kiboko\Bundle\DMSBundle\Form\Type\DocumentNodeType;
use Kiboko\Bundle\DMSBundle\Model\DocumentNodeInterface;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DocumentNodeController extends Controller
{
    /**
     * @Route("/", name="kiboko_dms_index")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Route("/create", name="kiboko_dms_create")
     * @Template("KibokoDMSBundle:DocumentNode:update.html.twig")
     */
    public function createAction(Request $request)
    {
        return $this->update($request, new DocumentNode());
    }

    /**
     * @Route("/{node}/edit", name="kiboko_dms_update")
     * @ParamConverter("node",
     *     class="KibokoDMSBundle:DocumentNode",
     *     options={
     *         "mapping": {"node": "id"},
     *         "map_method_signature" = true,
     *     }
     * )
     * @Template("KibokoDMSBundle:DocumentNode:update.html.twig")
     */
    public function editAction(Request $request, DocumentNodeInterface $node)
    {
        return $this->update($request, $node);
    }

    private function update(Request $request, DocumentNodeInterface $node)
    {
        $form = $this->createForm(DocumentNodeType::class, $node);

        /** @var $handler UpdateHandlerFacade */
        $handler = $this->get('oro_form.model.update_handler');
        return $handler->update(
            $node,
            $form,
            $this->get('translator')->trans('The Team Storage has been properly created')
        );
    }

    /**
     * @Route("/{node}", name="kiboko_dms_browse")
     * @ParamConverter("node",
     *     class="KibokoDMSBundle:DocumentNode",
     *     options={
     *         "mapping": {"node": "id"},
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
}
