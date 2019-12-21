<?php

namespace App\Tests;

use App\Controller\MusiqueController;
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
        $client->request('POST', 'https://localhost:8000/api_musique/musiques/titre/artiste/album/annee/genre');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test_Delete_musique_with_id(){
        $client = static::createClient();
        $client->request('DELETE', 'https://localhost:8000/api/musiques/delete/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test_Update_musique_with_id_and_parameters(){
        $client = static::createClient();
        $client->request('PUT', 'https://localhost:8000/api/musiques/modifier/2/titre/artiste/album/annee/genre');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test_Get_musique_file_with_id(){
        $client = static::createClient();
        $client->request('GET', 'https://localhost:8000/api/musiques/get/musique/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test_Get_image_file_with_id(){
        $client = static::createClient();
        $client->request('GET', 'https://localhost:8000/api/musiques/get/image/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
}