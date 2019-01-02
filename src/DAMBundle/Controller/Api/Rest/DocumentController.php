<?php


namespace Kiboko\Bundle\DAMBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Util\Codes;
use Kiboko\Bundle\DAMBundle\Entity\Document;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * @RouteResource("document")
 * @NamePrefix("kiboko_api_")
 */
class DocumentController extends RestController
{
    public function deleteAction(Document $document)
    {
        return $this->handleDeleteRequest($document->getId());
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
        return $this->get('kiboko_dam.document_manager.api');
    }
}