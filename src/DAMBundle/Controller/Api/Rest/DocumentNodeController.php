<?php


namespace Kiboko\Bundle\DAMBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Util\Codes;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Symfony\Component\HttpFoundation\Response;

/**
 * @RouteResource("document_node")
 * @NamePrefix("kiboko_api_")
 */
class DocumentNodeController extends RestController
{
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    public function getForm()
    {
        // This method is not needed to delete entities.
        //
        // Note: You will need to provide a proper implementation here
        // when you start working with more features of REST APIs.
    }

    public function getFormHandler()
    {
        // This method is not needed to delete entities.
        //
        // Note: You will need to provide a proper implementation here
        // when you start working with more features of REST APIs.
    }

    public function getManager()
    {
        return $this->get('kiboko.document_node_manager.api');
    }
}