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
                'titre' => $musique->getTitre(),
                'artiste' => $musique->getArtiste(),
                'album' => $musique->getAlbum(),
                'annee' => $musique->getAnnee(),
                'genre' => $musique->getGenre(),
                'poster' => $musique->getPathimage(),
                'src' => $musique->getPathmusique()
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
                'titre' => $musique->getTitre(),
                'artiste' => $musique->getArtiste(),
                'album' => $musique->getAlbum(),
                'annee' => $musique->getAnnee(),
                'genre' => $musique->getGenre(),
                'poster' => $musique->getPathimage(),
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
     * @Route("/musiques/ajouter/{titre}/{artiste}/{album}/{annee}/{genre}/{pathimage}/{pathmusique}", name="musique_ajout", methods={"POST"})
     */
    public function ajouterMusique($titre, $artiste, $album = null, $annee = null, $genre = null, $pathimage = null, $pathmusique){
        if($titre != null and $artiste != null and $pathmusique != null){
            $em = $this->getDoctrine()->getManager();
            $musique = new Musique();
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
                'artiste'    => $musique->getArtiste(),
                'titre' => $musique->getTitre()
                )
            ));
        }else{
            $reponse = new Response(json_encode('musique non valide'));
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
            'artiste'    => $musique->getArtiste(),
            'titre' => $musique->getTitre(),
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
            'artiste'    => $musique->getArtiste(),
            'titre' => $musique->getTitre()
            )
        ));

        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }
}
