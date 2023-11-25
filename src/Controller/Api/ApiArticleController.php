<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Article;
use App\Repository\ArticleRepository;
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

class ApiArticleController extends AbstractController
{
    ##[Route('/api/articles', name: 'api_article_index', methods: ['GET'])]
    /**
     * @return JsonResponse
     * @throws BadRequestHttpException
     */
    // public function apiArticleIndex(): JsonResponse
    // {
    //     $user = $this->getUser() ;

    //     if (is_null($user)) {
    //         return $this->json( ['message' => "Mauvais identifiant ", ], Response::HTTP_UNAUTHORIZED);
    //     }
        
    //     $user_data = [
    //         'email' => $user->getEmail() ,
    //         'nom'   => $user->getNom() ,
    //         'prenom'=> $user->getPrenom() ,
    //     ] ;

    //     return new JsonResponse($user_data, Response::HTTP_OK) ;
    //     //return $this->json($user_data) ;
    // }


    ##[Route('/api/article/{id}/show', name: 'api_article_show', methods: ['GET'])]
    /**
     * @return JsonResponse
     * @throws BadRequestHttpException
     */

    
    #[Route(path: '/api/category/{id}/article/new', name: 'api_article_new', methods: ['POST'])]
    /**
     * @return JsonResponse
     * @throws BadRequestHttpException
     */
    public function articleNew( CategoryRepository $categoryRepository ,$id, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator,SerializerInterface $serializer): JsonResponse
    {
        $category  = $categoryRepository->find($id) ;

        if ( !$category ){
            return new JsonResponse( $serializer->serialize( ['message' => "La catégorie n'existe pas."], 'json') , Response::HTTP_NOT_FOUND, [], true ) ;
        }

        $user = $this->getUser() ;
        if ( !$this->getUser() ){
            return new JsonResponse( $serializer->serialize( ['message' => "Veuillez vous connecter pour accèder à cette page."], 'json') , Response::HTTP_UNAUTHORIZED, [], true ) ;
        }

        // On convertit nos données json => Objet Php de type Category
        $article = $serializer->deserialize( $request->getContent(), Article::class, 'json' ) ;

        $article->setUser($user);
        $article->setCatgory($category);

        $entityManager->persist($article) ;
        $entityManager->flush();
        
        return new JsonResponse( $serializer->serialize( ['message' => "L'article a bien été créé."], 'json') , Response::HTTP_CREATED, ['accept' =>"application/json"], true ) ;
    }

    #[Route(path: '/api/article/{id}/delete', name: 'api_article_delete', methods: ['DELETE'])]
    /**
     * @return JsonResponse
     * @throws BadRequestHttpException
     */
    public function articleDelete( ArticleRepository $articleRepository , $id, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $article  = $articleRepository->find($id) ;
        
        if ( !$article ){
            return new JsonResponse( $serializer->serialize( ['message' => "L'article n'existe pas."], 'json') , Response::HTTP_NOT_FOUND, [], true ) ;
        }

        if ( !$this->getUser() )
        {
            return new JsonResponse( $serializer->serialize( ['message' => "Veuillez vous connecter pour accèder à cette page."], 'json') , Response::HTTP_UNAUTHORIZED, [], true ) ;
        }

        if ( $this->getUser() !== $article->getUser() )
        {
            return new JsonResponse( $serializer->serialize( ['message' => "Vous n'avez pas cette permission"], 'json') , Response::HTTP_UNAUTHORIZED, [], true ) ;
        }

        $entityManager->remove($article) ;
        $entityManager->flush() ;
        
        return new JsonResponse( null , Response::HTTP_NO_CONTENT, [], true ) ;
    }

    
    



}
