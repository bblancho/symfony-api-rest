<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Security\SecurityAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiRegisterController extends AbstractController
{
    #[Route('/api/inscription', name: 'api_register', methods: ['POST'])]
    /**
     * @param User|null $user
     * @return Response
     * @throws BadRequestHttpException
     */
    public function register( SerializerInterface $serializer, ValidatorInterface $validator , Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        if ( $this->getUser() ){
            return new JsonResponse( $serializer->serialize( ['message' => "Veuillez vous déconnecter pour accèder à cette page"], 'json') , Response::HTTP_UNAUTHORIZED, [], true ) ;
        }

        // On convertit nos données json => Objet Php de type user
        $newUser = $serializer->deserialize( $request->getContent(), User::class, 'json' ) ;

        $errors = $validator->validate($newUser) ;

        if( count($errors) > 0 ){
            // On convertit nos données PHP => Json
            return new JsonResponse( $serializer->serialize( $errors, 'json') , Response::HTTP_BAD_REQUEST, [], true ) ;
        }

        $getPassowrd = $newUser->getPassword() ;
        
        // encode the plain password
        $newUser->setPassword(
            $userPasswordHasher->hashPassword(
                $newUser, // ligne 35
                $getPassowrd
            )
        );

        $entityManager->persist($newUser);
        $entityManager->flush();

        return new JsonResponse( $serializer->serialize( ['message' => " Votre compte a bien été créé. "], 'json') , Response::HTTP_OK, ['accept' =>"application/json"], true ) ;
    }

}
