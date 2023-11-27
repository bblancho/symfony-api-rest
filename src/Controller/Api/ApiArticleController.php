<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Article;
use JMS\Serializer\Serializer;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
    #[Route(path: '/api/articles', name: 'api_article_index', methods: ['GET'])]
    /**
     * @return JsonResponse
     * @throws BadRequestHttpException
     */
    public function apiArticleIndex(SerializerInterface $serializer, ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): JsonResponse
    {
        // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri
        $data = $articleRepository->findBy( [], ['id' => 'desc'] ) ;

        // Objet knpPaginator
        $dataParPages = $paginator->paginate(
            $data, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            4 // Nombre de résultats par page
        );

        $data = [] ;

        foreach($dataParPages->getItems() as $key => $value ){
            $dataItems =[
                'article' => $value
            ]  ;

            $data[] = $dataItems ;
        }

        // On récupère seulement les éléments dont a besoin dans knpPaginator
        $getData = [
            'data' => $data ,
            'current_page_number' => $dataParPages->getCurrentPageNumber() ,
            'number_page_number'  => $dataParPages->getItemNumberPerPage() ,
            'total_account'       => $dataParPages->getTotalItemCount() ,
        ] ;

        $articlesJson = $serializer->serialize($getData, 'json') ;
        //dd($getData) ;

        return new JsonResponse( $articlesJson , Response::HTTP_OK, ['accept' => "application/json"], true) ;
    }

    
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
