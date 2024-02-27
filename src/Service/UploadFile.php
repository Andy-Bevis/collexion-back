<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UploadFile
{
    #[Route('api/secure/upload_file/{entity}',methods: ['POST'])]
    public function upload(Request $request, ParameterBagInterface $params, $entity)
    {
        $image = $request->files->get('file');

        if ($entity === 'user') {
            $image = $request->files->get('file');
        // on ajoute uniqid() afin de ne pas avoir 2 fichiers avec le même nom
        $newFilename = uniqid().'.'. $image->getClientOriginalName();
        // enregistrement de l'image dans le dossier public du serveur
        // paramas->get('public') =>  va chercher dans services.yaml la variable public
        // $image->move($params->get('images_users'), $newFilename);
        $url = "localhost/".$_SERVER["BASE"]."/images/users/".$newFilename;
        } elseif ($entity === 'collection') {
            $image = $request->files->get('file');
        // on ajoute uniqid() afin de ne pas avoir 2 fichiers avec le même nom
        $newFilename = uniqid().'.'. $image->getClientOriginalName();
        // enregistrement de l'image dans le dossier public du serveur
        // paramas->get('public') =>  va chercher dans services.yaml la variable public
        $image->move($params->get('images_collections'), $newFilename);
        $url = "localhost/".$_SERVER["BASE"]."/images/collections/".$newFilename;
        } elseif ($entity === 'object') {
            $image = $request->files->get('file');
        // on ajoute uniqid() afin de ne pas avoir 2 fichiers avec le même nom
        $newFilename = uniqid().'.'. $image->getClientOriginalName();
        // enregistrement de l'image dans le dossier public du serveur
        // paramas->get('public') =>  va chercher dans services.yaml la variable public
        $image->move($params->get('images_collections'), $newFilename);
        $url = "localhost/".$_SERVER["BASE"]."/images/collections/".$newFilename;
        }
        return new JsonResponse([
            'url' => $url,
        ]);
    }
}