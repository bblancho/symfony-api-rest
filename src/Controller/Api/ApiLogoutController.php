<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiLogoutController extends AbstractController
{
    #[Route(path: 'api/deconnexion33', name: 'api_logout', methods: ['GET'] )]
    public function logout(SerializerInterface $serializer): Response
    {
        if ( !$this->getUser() ){
            return new JsonResponse( $serializer->serialize( ['message' => "Veuillez vous connecter pour accèder à cette page"], 'json') , Response::HTTP_NOT_FOUND, [], true ) ;
        }

        return new JsonResponse(  ['message' => " Merci pour votre visite ", ] , Response::HTTP_OK) ;
    }
}
