<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Document\NeoDocument;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    // Tests home page
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('{"hello":"world!"}', $client->getResponse()->getContent());
    }
}
