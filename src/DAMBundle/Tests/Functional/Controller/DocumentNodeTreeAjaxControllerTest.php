<?php

namespace DAMBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DocumentNodeTreeAjaxControllerTest extends WebTestCase
{
    public function testDeleteAction()
    {
        $client = static::createClient();

        $client->request('GET', '/dam/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateAction()
    {
    }

    public function testResetAction()
    {
    }

    public function testMoveAction()
    {
    }
}
