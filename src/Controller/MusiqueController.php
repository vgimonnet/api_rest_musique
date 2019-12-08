<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Musique;



/**
 * @Route("/api_musique", name="api_musique")
 */
class MusiqueController extends AbstractController
{
    /**
     * @Route("/indexmusique", name="index_musique")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MusiqueController.php',
        ]);
    }

    /**
     * @Route("/musiques", name="musiques", methods={"GET"})
     */
    public function getAllMusiques(){
        $repository = $this->getDoctrine()->getRepository(Musique::class);
        $musiques = $repository->findAll();
        $listMusiques = [];

        foreach($musiques as $musique){
            $listMusiques[] = array(
                'id' => $musique->getId(),
                'title' => $musique->getTitre(),
                'artist' => $musique->getArtiste(),
                'album' => $musique->getAlbum(),
                'annee' => $musique->getAnnee(),
                'genre' => $musique->getGenre(),
                'pic' => "http://localhost:8000/api_musique/musiques/get/image/".$musique->getId(),
                'src' => "http://localhost:8000/api_musique/musiques/get/musique/".$musique->getId()
            ); 
        }

        $reponse = new Response();
        $reponse->setContent(json_encode($listMusiques));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }

    /**
     * @Route("/musiques/{id}", name="musiques_id", methods={"GET"})
     */
    public function getMusiqueId($id){
        $repository = $this->getDoctrine()->getRepository(Musique::class);
        $musique = $repository->find($id);

        if( !empty($musique)){
            $detailMusique = array(
                'id' => $musique->getId(),
                'title' => $musique->getTitre(),
                'artist' => $musique->getArtiste(),
                'album' => $musique->getAlbum(),
                'annee' => $musique->getAnnee(),
                'genre' => $musique->getGenre(),
                'pic' => $musique->getPathimage(),
                'src' => $musique->getPathmusique()
            );
        }        

        $reponse = new Response();
        $reponse->setContent(json_encode($detailMusique));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }

    /**
     * @Route("/musiques/ajouter", name="musique_ajout", methods={"POST"})
     */
    public function ajouterMusique(){

        if($_POST['titre'] != null and $_POST['titre'] != null and $_FILES['musique']['name'] != null){
            $em = $this->getDoctrine()->getManager();
            $musique = new Musique();
            $musique->setTitre($_POST['titre']);
            $musique->setArtiste($_POST['artiste']);

            if($_POST['album'] != null){
                $musique->setAlbum($_POST['album']);
            }else{
                $musique->setAlbum(null);
            }

            if($_POST['annee'] != null){
                $musique->setAnnee($_POST['annee']);
            }else{
                $musique->setAnnee(null);
            }
            
            if($_POST['genre'] != null){
                $musique->setGenre($_POST['genre']);
            }else{
                $musique->setGenre(null);
            }
            
            if($_FILES['image']['name'] != null){
                $musique->setPathimage($_FILES['image']['name']);
                if(isset($_FILES['image']['type'])){
                    move_uploaded_file($_FILES['image']['tmp_name'], '../public/Images/'.$_FILES['image']['name']);
                }
            }else{
                $musique->setPathimage(null);
            }

            
            $musique->setPathmusique($_FILES['musique']['name']);
            if(isset($_FILES['musique']['type'])){
                move_uploaded_file($_FILES['musique']['tmp_name'], '../public/Musiques/'.$_FILES['musique']['name']);
            }


    
            $em->persist($musique);
            $em->flush();
    
            $reponse = new Response(json_encode(array(
                'id'     => $musique->getId(),
                'artist'    => $musique->getArtiste(),
                'title' => $musique->getTitre()
                )
            ));
        }else{
            $reponse = new Response(json_encode('state: musique non valide'));
        }
        
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }

    /**
     * @Route("/musiques/delete/{id}", name="musique_delete", methods={"DELETE"})
     */
    public function deleteMusique($id){
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Musique::class);
        $musique = $repository->find($id);
        $em->remove($musique);
        $em->flush();

        $reponse = new Response(json_encode(array(
            'artist'    => $musique->getArtiste(),
            'title' => $musique->getTitre(),
            ))
        );
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }

    /**
     * @Route("/musiques/modifer/{id}/{titre}/{artiste}/{album}/{annee}/{genre}/{pathimage}/{pathmusique}", name="musique_modify", methods={"UPDATE"})
     */
    public function modifierMusique($id, $titre, $artiste, $album, $annee, $genre, $pathimage, $pathmusique){
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Musique::class);
        $musique = $repository->find($id);
        
        $musique->setTitre($titre);
        $musique->setArtiste($artiste);
        $musique->setAlbum($album);
        $musique->setAnnee($annee);
        $musique->setGenre($genre);
        $musique->setPathimage($pathimage);
        $musique->setPathmusique($pathmusique);

        $em->persist($musique);
        $em->flush();

        $reponse = new Response(json_encode(array(
            'id'     => $musique->getId(),
            'artist'    => $musique->getArtiste(),
            'title' => $musique->getTitre()
            )
        ));

        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }

    /**
     * @Route("/musiques/get/musique/{id}", name="get_musique", methods={"GET"})
     */
    public function getMusiqueFile($id){

        $repository = $this->getDoctrine()->getRepository(Musique::class);
        $musique = $repository->find($id);

        $file = "../public/Musiques/".$musique->getPathmusique();        

        return new \Symfony\Component\HttpFoundation\BinaryFileResponse($file);
    }

    /**
     * @Route("/musiques/get/image/{id}", name="get_image", methods={"GET"})
     */
    public function getImageFile($id){

        $repository = $this->getDoctrine()->getRepository(Musique::class);
        $musique = $repository->find($id);

        $file = "../public/Images/".$musique->getPathimage();
        
        return new \Symfony\Component\HttpFoundation\BinaryFileResponse($file);
    }
}
