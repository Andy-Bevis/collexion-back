<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Comment;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;

// root URL for all routes from MyCollectionController
#[Route('/api')]
class UserController extends AbstractController
{
    /**
     * list all users
     *
     * @return Response
     */
    #[Route('/users', name: 'api_user_list',methods: ['GET'])]
    public function list(UserRepository $userRepository): Response
    {
        // retrieve all users
        $users = $userRepository->findAll();

        // check if $user doesn't exist
        if (!$users) {
            return $this->json(
                "Error : User inexistant",
                // status code
                404
            );
        }

        // return json
        return $this->json(
            // what I want to show
            $users,
            // status code
            200,
            // header
            ['Access-Control-Allow-Origin' => '*' ],
            // groups authorized
            ['groups' => 'get_users']
        );
    }

    /**
     * show one user by its id
     *
     * @return Response
     */
    #[Route('/user/{id}', name: 'api_user_show',methods: ['GET'])]
    public function show(User $user): Response
    {
        // check if $user doesn't exist
        if (!$user) {
            return $this->json(
                "Error : User inexistant",
                // status code
                404
            );
        }

        // return json
        return $this->json(
            // what I want to show
            $user,
            // status code
            200,
            // header
            ['Access-Control-Allow-Origin' => '*' ],
            // groups authorized
            ['groups' => 'get_user']
        );
    }

        /**
    * update one user
    *
    * @param UserRepository $userRepository
    * @return Response
    */
    #[Route('/user/{id}', name: 'api_user_update',methods: ['PUT'])]
    public function update(User $user = null,Request $request, EntityManagerInterface $entityManager,SerializerInterface $serializer,  UserPasswordHasherInterface $passwordHasher): Response
    {
        // check if $user doesn't exist
        if (!$user) {
            return $this->json(
                "Error : User inexistant",
                // status code
                404
            );
        }

    // Désérialiser les données de la requête PUT dans un objet User
    $userUpdateRequest = $serializer->deserialize($request->getContent(), User::class, 'json');

    // Mettre à jour les propriétés de l'utilisateur existant avec les données de l'objet User
    $user->setEmail($userUpdateRequest->getEmail());
    $user->setNickname($userUpdateRequest->getNickname());
    $user->setDescription($userUpdateRequest->getDescription());
    $user->setPicture($userUpdateRequest->getPicture());
    
    // Vérifier et mettre à jour le mot de passe si nécessaire
    if ($userUpdateRequest->getPassword()) {
        $hashedPassword = $passwordHasher->hashPassword($user, $userUpdateRequest->getPassword());
        $user->setPassword($hashedPassword);
    }

        $entityManager->flush();
 
        return $this->json(['message' => 'updated successful', 200]);
 
    }

    /**
    * delete one user
    * 
    * @param UserRepository $userRepository
    * @return Response
    */
    #[Route('/user/{id}', name: 'api_user_delete', methods: ['DELETE'])]
    public function delete(User $user = null , EntityManagerInterface $entityManager): Response
    {
         // check if $user doesn't exist
        if (!$user) {
            return $this->json(
                ['message' => 'User inexistant'],
                404,
                );
        }
        // delete 
        $entityManager->remove($user);
        // record in database
        $entityManager->flush();

        return $this->json(['message' => 'delete successful', 200]);
       
    }
    
}
