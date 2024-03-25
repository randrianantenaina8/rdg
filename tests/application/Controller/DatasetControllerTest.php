<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
* DatasetController Application Tests 
*
* @group Application
*
* @author Mirko Venturi
*/
final class DatasetControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/jeux-de-donnees');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Les Jeux de données');
    }
}
