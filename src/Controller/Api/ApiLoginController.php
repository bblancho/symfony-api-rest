<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    /**
     * @param User|null $user
     * @return Response
     * @throws BadRequestHttpException
     */
    public function apiLogin( #[CurrentUser] ?User $user): Response
    {
        $user = $this->getUser() ;

        if (is_null($user)) {
            return $this->json( ['message' => "Mauvais identifiant ", ], Response::HTTP_UNAUTHORIZED);
        }
        
        $user_data = [
            'email' => $user->getEmail() ,
            'nom'   => $user->getNom() ,
            'prenom'=> $user->getPrenom() ,
        ] ;

        return new JsonResponse($user_data, Response::HTTP_OK) ;
        //return $this->json($user_data) ;
    }
}
