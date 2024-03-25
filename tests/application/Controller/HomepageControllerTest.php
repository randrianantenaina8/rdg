<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
* HomepageController Application Tests 
*
* @group Application
*
* @author Mirko Venturi
*/
final class HomepageControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Accueil recherche.data.gouv');
    }
}
