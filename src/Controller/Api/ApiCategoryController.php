<?php

namespace App\Controller\Api;

use App\Entity\User;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiCategoryController extends AbstractController
{
    #[Route('/api/category/new', name: 'api_category_add', methods: ['POST'])]
    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function apiAddCategory( SerializerInterface $serializer, ValidatorInterface $validator , Request $request,)
    {
        // On convertit nos données json => Objet Php de type user
        $newUser = $serializer->deserialize( $request->getContent(), User::class, 'json' ) ;

        $errors = $validator->validate($newUser) ;

        if( count($errors) > 0 ){
            // On convertit nos données PHP => Json
            return new JsonResponse( $serializer->serialize( $errors, 'json') , Response::HTTP_BAD_REQUEST, [], true ) ;
        }
    }
}
