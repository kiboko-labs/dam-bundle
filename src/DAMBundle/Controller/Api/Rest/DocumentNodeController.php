<?php

namespace Kiboko\Bundle\DAMBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;

/**
 * @RouteResource("document_node")
 * @NamePrefix("kiboko_api_")
 */
final class DocumentNodeController extends RestController
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
