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

    	if ($password != null) {
    		$password = password_hash($password, PASSWORD_DEFAULT);
    		$user->setPassword($password);
    	}else{
				// pwd par défault = 123
				$password = "123";
				$password = password_hash($password, PASSWORD_DEFAULT);
				$user->setPassword($password);
    	}

    	if($email != null){
    		$user->setEmail($email);
    	}else{
    		$user->setEmail(null);
			}
			
    	$user->setAdmin(0);
			
        // on enregistre
    	$em->persist($user);
    	$em->flush();

        // on recupére les données de la base
    	$reponse = new Response(json_encode(array(
    		'id'     => $user->getId(),
    		'username'    => $user->getUsername(),
    		'nom' => $user->getNom(),
    		'prenom'    => $user->getPrenom(),
    		'email'    => $user->getEmail()
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

    	if ($username != null) {
    		$user->setUsername($username);
    	}else{
    		$user->setUsername($user->getUsername());
    	}	    	

    	if($nom != null){
    		$user->setNom($nom);
    	}else{
    		$user->setNom($user->getNom());
    	}

    	if($prenom != null){
    		$user->setPrenom($prenom);
    	}else{
    		$user->setPrenom($user->getPrenom());
    	}

    	if ($password != null) {
    		$password = password_hash($password, PASSWORD_DEFAULT);
    		$user->setPassword($password);
    	}else{
    		$user->setPassword($user->getPassword());
    	}
    	
    	if($email != null){
    		$user->setEmail($email);
    	}else{
    		$user->setEmail($user->getEmail());
    	}

    	$em->persist($user);
    	$em->flush();

    	$reponse = new Response(json_encode(array(
    		'id'     => $user->getId(),
    		'username'    => $user->getUsername(),
    		'nom' => $user->getNom(),
    		'prenom'    => $user->getPrenom(),
    		'email'    => $user->getEmail(),
    	)
    ));

    	$reponse->headers->set("Content-Type", "application/json");
    	$reponse->headers->set("Access-Control-Allow-Origin", "*");
    	return $reponse;
    }

    // modif password avec route

	/**
     * @Route("/update/{id}{password}", name="upd_pwd", methods={"PUT"})
     */
	public function updateMdpUser($id, $password)
	{
		$em = $this->getDoctrine()->getManager();
		$repository = $this->getDoctrine()->getRepository(User::class);
		$user = $repository->find($id);

		$password = password_hash($password, PASSWORD_DEFAULT);
		$user->setPassword($password);

		$em->persist($user);
		$em->flush();

		$reponse = new Response(json_encode(array(
			'id'     => $user->getId(),
			'username'    => $user->getUsername(),
			'nom' => $user->getNom(),
			'prenom'    => $user->getPrenom(),
			'email'    => $user->getEmail(),
			'admin' 	=> $user->getAdmin()
		)
	));

		$reponse->headers->set("Content-Type", "application/json");
		$reponse->headers->set("Access-Control-Allow-Origin", "*");
		return $reponse;
	}

    // connexion

    /**
     * @Route("/connexion/{username}/{password}", name="connexion_user", methods={"GET"})
     */
    public function connexion($username, $password)
    {
    	$repository = $this->getDoctrine()->getRepository(User::class);
    	$user = $repository->findOneBy(['username' => $username]);

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
					$username = $user->getUsername();
				}else{ 
					$username = 'anonymous'; 
				}
    		$reponse = new Response(json_encode(array(
    			'id' => $user->getId(),
					'username' => $username,
					'prenom' => $user->getPrenom(),
					'nom' => $user->getNom(),
					'email' => $user->getEmail(),
					'verif' => $verif,
					'admin' => $user->getAdmin()
					))
        ); 
			

			
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
    			'admin'=> $user->getAdmin()
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
     * @Route("/users", name="user", methods={"GET"})
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
    			'admin'=> $user->getAdmin()
    		); 
    	}

    	$reponse = new Response();
    	$reponse->setContent(json_encode($listUser));
    	$reponse->headers->set("Content-Type", "application/json");
    	$reponse->headers->set("Access-Control-Allow-Origin", "*");
    	return $reponse;
    }
}
