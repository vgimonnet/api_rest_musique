<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

/**
 * @Route("/user", name="user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/indexUser", name="indexUser")
     */
    public function index()
    {
    	return $this->json([
    		'message' => 'Welcome to your new controller User !',
    		'path' => 'src/Controller/UserController.php',
    	]);
    }


    // ajout

    /**
     * @Route("/ajout/{username}/{nom}/{prenom}/{password}/{email}", name="ajout_user", methods={"POST"})
     */
    public function ajoutUser($username, $nom, $prenom, $password, $email){

    	$em = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository(User::class);
    	$user = new User();

        // récupération des données
    	$user->setUsername($username);

    	if($nom != null){
    		$user->setNom($nom);
    	}else{
    		$user->setNom("Pas de nom");
    	}

    	if($prenom != null){
    		$user->setPrenom($prenom);
    	}else{
    		$user->setPrenom("Pas de prenom");
    	}

    	$password = password_hash($password, PASSWORD_DEFAULT);
    	$user->setPassword($password);

    	if($email != null){
    		$user->setEmail($email);
    	}else{
    		$user->setEmail(null);
    	}

    	$datetime = date("Y-m-d H:i:s");
    	$user->setDateCreation($datetime);

        // on enregistre
    	$em->persist($user);
    	$em->flush();

        // on recupére les données de la base
    	$reponse = new Response(json_encode(array(
    		'id'     => $user->getId(),
    		'username'    => $user->getUsername(),
    		'nom' => $user->getNom(),
    		'prenom'    => $user->getPrenom(),
    		'email'    => $user->getEmail(),
    		'dateCreation'    => $user->getDateCreation()
    	)
    ));
    	$reponse->headers->set("Content-Type", "application/json");
    	$reponse->headers->set("Access-Control-Allow-Origin", "*");
    	return $reponse;

    }

    // supression

    /**
     * @Route("/suppression/{id}", name="supp_user", methods={"DELETE"})
     */
    public function suppressionUser($id){

    	$em = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository(User::class);
    	$user = $repository->find($id);
    	$em->remove($user);
    	$em->flush();

    	$reponse = new Response(json_encode(array(
    		'id'     => $user->getId(),
    		'username'    => $user->getUsername(),
    		'nom' => $user->getNom(),
    		'prenom'    => $user->getPrenom(),
    		'email'    => $user->getEmail(),
    		'dateCreation'    => $user->getDateCreation()
    	)
    ));
    	$reponse->headers->set("Content-Type", "application/json");
    	$reponse->headers->set("Access-Control-Allow-Origin", "*");
    	return $reponse;
    }

    // modification

    /**
     * @Route("/update/{id}/{username}/{nom}/{prenom}/{password}/{email}", name="upd_user", methods={"PUT"})
     */
    public function updateUser($id, $username, $nom, $prenom, $password, $email){

    	$em = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository(User::class);
    	$user = $repository->find($id);

    	$user->setUsername($username);

    	if($nom != null){
    		$user->setNom($nom);
    	}else{
    		$user->setNom("Nom non update");
    	}

    	if($prenom != null){
    		$user->setPrenom($prenom);
    	}else{
    		$user->setPrenom("Prenom non update");
    	}

    	updateMdpUser($password);

    	if($email != null){
    		$user->setEmail($email);
    	}else{
    		$user->setEmail(null);
    	}

    	$datetime = date("Y-m-d H:i:s");
    	$user->setDateCreation($datetime);

    	$em->persist($user);
    	$em->flush();

    	$reponse = new Response(json_encode(array(
    		'id'     => $user->getId(),
    		'username'    => $user->getUsername(),
    		'nom' => $user->getNom(),
    		'prenom'    => $user->getPrenom(),
    		'email'    => $user->getEmail(),
    		'dateCreation'    => $user->getDateCreation()
    	)
    ));

    	$reponse->headers->set("Content-Type", "application/json");
    	$reponse->headers->set("Access-Control-Allow-Origin", "*");
    	return $reponse;
    }

    // connexion

    /**
     * @Route("/connexion/{id}/{username}/{password}", name="connexion_user", methods={"GET"})
     */
    public function connexion($username, $password)
    {
    	$repository = $this->getDoctrine()->getRepository(User::class);
    	$user = $repository->find($username);

    	$pass = $user->getPassword();

    	if (password_verify($password, $pass))
    	{
    		$verif = true;
    	}
    	else
    	{
    		$verif = false;
    	}

    	if( !empty($user)){
    		$detailUser = array(
    			'id' => $user->getId(),
    			'username' => $user->getUsername(),
    			'verif' => $verif
    		);
    	} 

    	$reponse->headers->set("Content-Type", "application/json");
    	$reponse->headers->set("Access-Control-Allow-Origin", "*");
    	return $reponse;
    }

    //get un user

    /**
     * @Route("/user/{id}", name="user_id", methods={"GET"})
     */
    public function getUserId($id){
    	$repository = $this->getDoctrine()->getRepository(User::class);
    	$user = $repository->find($id);

    	if( !empty($user)){
    		$detailUser = array(
    			'id' => $user->getId(),
    			'username' => $user->getUsername(),
    			'nom' => $user->getNom(),
    			'prenom' => $user->getPrenom(),
    			'email' => $user->getEmail(),
    			'date_creation' => $user->getDateCreation()
    		);
    	}        

    	$reponse = new Response();
    	$reponse->setContent(json_encode($detailUser));
    	$reponse->headers->set("Content-Type", "application/json");
    	$reponse->headers->set("Access-Control-Allow-Origin", "*");
    	return $reponse;
    }

    // get tout les users

    /**
     * @Route("/user", name="user", methods={"GET"})
     */
    public function getAllUsers(){
    	$repository = $this->getDoctrine()->getRepository(User::class);
    	$users = $repository->findAll();
    	$listUser = [];

    	foreach($users as $user){
    		$listUser[] = array(
    			'id' => $user->getId(),
    			'username' => $user->getUsername(),
    			'nom' => $user->getNom(),
    			'prenom' => $user->getPrenom(),
    			'email' => $user->getEmail(),
    			'date_creation' => $user->getDateCreation()
    		); 
    	}

    	$reponse = new Response();
    	$reponse->setContent(json_encode($listMusiques));
    	$reponse->headers->set("Content-Type", "application/json");
    	$reponse->headers->set("Access-Control-Allow-Origin", "*");
    	return $reponse;
    }


    // modif password

    public function updateMdpUser($id)
    {
    	# code...
    }








    // il manque updateMdpUser et mauvais code dans updateUser pour updateMdpUser ligne 138
}
