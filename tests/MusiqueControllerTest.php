<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MusiqueControllerTest extends WebTestCase{

    public function test_Get_All_Musiques(){
        $client = static::createClient();
        $client->request('GET', 'https://localhost:8000/api_musique/musiques');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test_Get_Musique_With_Id(){
        $client = static::createClient();
        $client->request('GET', 'https://localhost:8000/api_musique/musiques/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test_Post_Musiques(){
        $client = static::createClient();
        $client->request('POST', 'https://localhost:8000/api_musique/musiques/ajouter/test/test/null/null/null/null/test');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
}