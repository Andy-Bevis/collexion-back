<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Entity\MyObject;
use App\Repository\CategoryRepository;
use App\Repository\MyCollectionRepository;
use App\Repository\MyObjectRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

// all comment avialable on MyCollectionController
#[Route('/api')]
class MyObjectController extends AbstractController
{
    
    /**
     * list all objects
     *
     * @param MyObjectRepository $myObjectsRepository
     * @return Response
     */
    #[Route('/objects', name: 'api_my_object_list')]
    public function list(MyObjectRepository $myObjectRepository): Response
    {
        $objects = $myObjectRepository->findAll();
        
        if (! $objects) {
            return $this->json(
                "Error :    Objets inexistants",
                404
            );
        }

        return $this->json(
            $objects,
            200,
            [],
            ['groups' => 'get_objects']
        );
    }

     /**
     * list one object by its id
     *
     * @param MyObjectRepository $myObjectRepository
     * @return Response
     */
    #[Route('/object/{id}', name: 'api_my_object_show',methods: ['GET'])]
    public function show(MyObject $myObject): Response
    {
        if (!$myObject) {
            return $this->json(
                "Error : Objet inexistant",
                404
            );
        }
        return $this->json(
            $myObject,
            200,
            [],
            ['groups' => 'get_page_object']
            );
    } 

    /**
    * create one object 
    *
    * @param MyObjectRepository $myObjectRepository
    * @return Response
    */
   #[Route('/secure/object', name: 'api_my_object_create',methods: ['POST'])]
   public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator, Category $category = null, CategoryRepository $categoryRepository, MyCollectionRepository $myCollectionRepository,)
   {

    $jsonData = json_decode($request->getContent(), true);
    $myNewObject = $serializer->deserialize($request->getContent(), MyObject::class, 'json');

    $categoryId = $jsonData['relatedCategory'];
    $category = $categoryRepository->find($categoryId);

    if (!$category) {
        return $this->json(['message' => 'Category not found'], 404);
    }

    $myCollectionId = $jsonData['relatedMyCollections'];

    $myObject = new MyObject();
    $myObject->setCategory($category);
    $myObject->setName($myNewObject->getName());
    $myObject->setDescription($myNewObject->getDescription());
    $myObject->setImage($myNewObject->getImage());
    $myObject->setUpdatedAt(New DateTimeImmutable());
    $myObject->setState($myNewObject->getState());
    foreach ($myCollectionId as $collection) {
        $collectionId = $collection['id'];
        $collectionToAdd = $myCollectionRepository->find($collectionId);
        if ($collectionToAdd) {
            $myObject->addMyCollection($collectionToAdd);
        }
    }
    
    $violations = $validator->validate($myObject);

    if (0 !== count($violations)) {
        return $this->json([$violations, 500, ['message' => 'error']]);
    } else {
        $entityManager->persist($myObject);
        $entityManager->flush();

        return $this->json($serializer->serialize($myObject, 'json', ['groups' => 'object']), 201, ['message' => 'create successful']);
    }
   }

    /**
    * update one object
    *
    * @param MyObjectRepository $myObjectRepository
    * @return Response
    */
    #[Route('/secure/object/{id}', name: 'api_my_object_update',methods: ['PUT'])]
    public function update(MyObject $myObject = null, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, SerializerInterface $serializer , Request $request, MyCollectionRepository $myCollectionRepository, ValidatorInterface $validator): Response
    {
        if (!$myObject) {
            return $this->json(
                "Error : Objet inexistant",
                404
            );
        }

        $jsonData = json_decode($request->getContent(), true);
        $updateMyObject = $serializer->deserialize($request->getContent(), MyObject::class, 'json');

        $categoryId = $jsonData['relatedCategory'];
        $updateCategory = $categoryRepository->find($categoryId);

        if (!$updateCategory) {
            return $this->json(['message' => 'Category not found'], 404);
        }

        $myCollectionId = $jsonData['relatedMyCollections'];

        $myObject->setCategory($updateCategory);
        $myObject->setName($updateMyObject->getName());
        $myObject->setDescription($updateMyObject->getDescription());
        $myObject->setImage($updateMyObject->getImage());
        $myObject->setUpdatedAt(New DateTimeImmutable());
        $myObject->setState($updateMyObject->getState());

        foreach ($myCollectionId as $collection) {
            $collectionId = $collection['id'];
            $collectionToAdd = $myCollectionRepository->find($collectionId);
            if ($collectionToAdd) {
                $myObject->addMyCollection($collectionToAdd);
            } else {
                return $this->json(['message' => 'Collection not found'], 404);
            }
        }

        $violations = $validator->validate($myObject);

        if (0 !== count($violations)) {
            return $this->json([$violations,500,['message' => 'error']]); ;
        } else{

            $entityManager->flush();
            return $this->json($serializer->serialize($myObject, 'json', ['groups' => 'object']), 200, ['message' => 'update successful']);
        }
    }
    
    /**
    * delete one object
    * 
    * @param MyObjectRepository $myObjectRepository
    * @return Response
    */
    #[Route('/secure/object/{id}', name: 'api_my_object_delete', methods: ['DELETE'])]
    public function delete(MyObject $Object, EntityManagerInterface $manager): Response
    {
        if (!$Object) {
            return $this->json(
                ['message' => 'objet inexistant'],
                404,
                );
        }

        $manager->remove($Object);
        $manager->flush();

        return $this->json(['message' => 'delete successful', 200]);
       
    }

    #[Route('/secure/object/upload_file', name: 'api_object_upload_file', methods: ['POST'])]
    public function upload(Request $request, ParameterBagInterface $params): Response
    {
        $image = $request->files->get('file');
        // on ajoute uniqid() afin de ne pas avoir 2 fichiers avec le même nom
        $newFilename = uniqid().'.'. $image->getClientOriginalName();
        // enregistrement de l'image dans le dossier public du serveur
        // paramas->get('public') =>  va chercher dans services.yaml la variable public
        $image->move($params->get('images_objects'), $newFilename);
        $url = $_SERVER["BASE"]."/images/objects/".$newFilename;

        return $this->json([
            'url' => $url
        ]);
    }
    
  
    #[Route('/object_random', name: 'api_my_object_random',methods: ['GET'])]
    public function random(MyObjectRepository $myObjectRepository): Response
    {
        // retrieve all collections
        $objects = $myObjectRepository->findRandomObjectSql();

        foreach ($objects as $object) {
            $objectRandom = $myObjectRepository->find($object['id']);
            $objectsRandom[] = $objectRandom;
        }
        
        // check if $myCollection doesn't exist
        if (!$objectRandom) {
            return $this->json(
                "Error : Objet inexistant",
                // status code
                404
            );
        }

        // return json
        return $this->json(
            // what I want to show
            $objectsRandom,
            // status code
            200,
            // header
            [],
            // groups authorized
            ['groups' => 'get_objects']
        );
    }
}