<?php

namespace App\Tests\Controller;

use App\Controller\Front\ProjectTeamController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
* ProjectTeamController Application Tests 
*
* @group Application
*
* @author Mirko Venturi
*/
final class ProjectTeamControllerTest extends WebTestCase
{
    public function testList(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/entrepot-pluridisciplinaire');
        
        $rowsNumber = count($crawler->filter('row'));
        $this->assertGreaterThanOrEqual($rowsNumber, 1);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Entrepôt Recherche Data Gouv');
    }
}
