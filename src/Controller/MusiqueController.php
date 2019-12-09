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
     * @Route("/musiques/ajouter/{titre}/{artiste}/{album}/{annee}/{genre}", name="musique_ajout", methods={"POST"})
     * @param Request $request
     */
    public function ajouterMusique($titre, $artiste, $album, $annee, $genre, Request $request){

        header("Access-Control-Allow-Origin: *");
        $em = $this->getDoctrine()->getManager();
        $musique = new Musique();
        if($titre != null){

            if($request->files->get('mp3') != null){
                $get_mp3 = $request->files->get('mp3');
                $temp_mp3 = pathinfo($get_mp3->getClientOriginalName(), PATHINFO_FILENAME);
                $mp3_name = $temp_mp3.'.'.$get_mp3->getClientOriginalExtension();
                
                $get_mp3->move(
                    "../public/Musiques/",
                    $mp3_name
                );
                $musique->setPathmusique($mp3_name);
            }else{
                $musique->setPathmusique('null');
            }

            if($request->files->get('pic')){
                $get_pic = $request->files->get('pic');
                $temp_pic = pathinfo($get_pic->getClientOriginalName(), PATHINFO_FILENAME);
                $pic_name = $temp_pic.'.'.$get_pic->getClientOriginalExtension() ;
                $get_pic->move(
                    "../public/Images/",
                    $pic_name
                );
                $musique->setPathimage($pic_name);
            }else{
                $musique->setPathimage('null');
            }
            
            $musique = new Musique();
            $musique->setTitre($titre);

            if($artiste != null){
                $musique->setArtiste($artiste);
            }else{
                $musique->setArtiste(null);
            }

            if($album != null){
                $musique->setAlbum($album);
            }else{
                $musique->setAlbum(null);
            }

            if($annee != null){
                $musique->setAnnee($annee);
            }else{
                $musique->setAnnee(null);
            }
            
            if($genre != null){
                $musique->setGenre($genre);
            }else{
                $musique->setGenre(null);
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
     * @Route("/musiques/modifer/{id}/{titre}/{artiste}/{album}/{annee}/{genre}", name="musique_modify", methods={"UPDATE"})
     */
    public function modifierMusique($id, $titre, $artiste, $album, $annee, $genre){
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Musique::class);
        $musique = $repository->find($id);
        
        $musique->setTitre($titre);
        $musique->setArtiste($artiste);
        $musique->setAlbum($album);
        $musique->setAnnee($annee);
        $musique->setGenre($genre);

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
