<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
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
    #[Route(path:"/api/category", name: 'api_category_index', methods: ['GET'])]
    public function apiCategoryIndexe(SerializerInterface $serializer, CategoryRepository $catRepository): JsonResponse
    {
        if ( !$this->getUser() ){
            return new JsonResponse( $serializer->serialize( ['message' => "Veuillez vous connecter pour accèder à cette page"], 'json') , Response::HTTP_UNAUTHORIZED, [], true ) ;
        }
        
        $categories = $catRepository->findAll() ;

        $jsonCategories = $serializer->serialize( $categories , 'json') ;

        return new JsonResponse( $jsonCategories, Response::HTTP_OK, ['accept' =>"application/json"], true)  ;
    }


    #[Route('/api/category/new', name: 'api_category_add', methods: ['POST'])]
    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function apiAddCategory( SerializerInterface $serializer, ValidatorInterface $validator , Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // On convertit nos données json => Objet Php de type user
        $category = $serializer->deserialize( $request->getContent(), Category::class, 'json' ) ;

        $errors = $validator->validate($category) ;

        if( count($errors) > 0 ){
            // On convertit nos données PHP => Json
            return new JsonResponse( $serializer->serialize( $errors, 'json') , Response::HTTP_BAD_REQUEST, [], true ) ;
        }

        $nom = $category->getNom() ;

        $entityManager->persist($category) ;
        $entityManager->flush();

        return new JsonResponse( $serializer->serialize( ['message' => " La categorie $nom a bien été créé. "], 'json') , Response::HTTP_OK, ['accept' =>"application/json"], true ) ;
    }

    #[Route(path:"/api/category/{id}/delete", name: 'api_category_delete', methods: ['DELETE'])]
    public function apiDeleteCategory($id, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        if ( !$this->getUser() ){
            return new JsonResponse( $serializer->serialize( ['message' => "Veuillez vous connecter pour accèder à cette page"], 'json') , Response::HTTP_UNAUTHORIZED, [], true ) ;
        }
        
        $catRepository = $entityManager->getRepository(Category::class);
        $category = $catRepository->find($id) ;

        if ( !$category ){
            return new JsonResponse( $serializer->serialize( ['message' => "La catégorie n'existe pas"], 'json') , Response::HTTP_NOT_FOUND, [], true ) ;
        }

        $entityManager->remove($category) ;
        $entityManager->flush() ;

        return new JsonResponse( null, Response::HTTP_NO_CONTENT)  ;
    }

}
