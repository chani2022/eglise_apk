<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comments;
use App\Entity\Langue;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Repository\CommentsRepository;
use App\Repository\GalerieRepository;
use App\Repository\LangueRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api_articles_get')]
    public function index(Request $request, ArticleRepository $articleRep, CategorieRepository $categRep, GalerieRepository $galRep, LangueRepository $langueRep, CommentsRepository $commentsRepository, EntityManagerInterface $em): JsonResponse
    {
        $type = $request->query->get('type');
        $firstResult = $request->query->get('firstResult');
        $locale = $request->query->get('locale');
        $categorie = $categRep->findOneBy(["type" => $type]);
        $langue = $langueRep->findOneBy(["type" => $locale]);
        $articles = $articleRep->findByCategorieArticleWithLimit($categorie, $firstResult, $langue);

        $data = [];
        foreach ($articles as $i => $articleP) {
            $data[] = [
                "id_article" => $articleP['id'],
                "ids_unread_article" => $articleP['ids_unread_notification'],
                "email_author" => $articleP["user"]['email'],
                "date_created" => $articleP['updated_at'],
                "titre" => $articleP['titre'],
                "commentaire" => $articleP['commentaire'],
                "categorie_article" => $articleP['categorie']['type'],
                "langue_article" => $articleP["langue"]['type'],
                "image_article" => $articleP['image'],
                "extrait_commentaire" => substr($articleP['commentaire'], 0, 100),
                "date_event" => $articleP['event_at'],
                'nb_comments' => 0,
                'comments' => []
            ];
            $galeries = $galRep->findBy([
                "article" => $articleRep->find($articleP['id'])
            ]);
            if ($galeries) {
                foreach ($galeries as $galerie) {
                    $data[$i]["galeries"][] = $galerie->getNomImage();
                }
            }
            $article = $articleRep->find($articleP['id']);

            $comments = $commentsRepository->findBy([
                "article" => $article
            ]);
            if (count($comments) > 0) {
                $data[$i]['nb_comments'] = count($comments);
                foreach ($comments as $comment) {
                    $data[$i]['comments'][] = [
                        "user" => $comment->getUser()->getNom() ? $comment->getUser()->getNom() . ' ' . $comment->getUser()->getPrenom() : $comment->getUser()->getUsername(),
                        "date" => $comment->getCreatedAt(),
                        "contenu" => $comment->getContenu()
                    ];
                }
            }
        }
        return $this->json($data);
    }
    #[Route('/api/create/comments', name: 'app_create_comments')]
    public function insertComments(Request $request, ArticleRepository $articleRepository, EntityManagerInterface $em, UserRepository $userRepository): JsonResponse
    {
        $id_article = $request->request->get('id_article');
        $article = $articleRepository->find($id_article);


        $id_user = $request->request->get('id_user');
        $user = $userRepository->find($id_user);

        $contenu = $request->request->get('value');


        $comment = new Comments();
        $comment->setArticle($article)
            ->setContenu($contenu)
            ->setUser($user);

        $em->persist($comment);
        $em->flush();

        $data = $this->serializeData($comment);

        return $this->json($data);
    }
    #[Route("/api/comments/{article}/{firstResult}", name: "app_comments")]
    public function getCommentsArticle(Article $article, $firstResult, Request $request, CommentsRepository $commentsRepository): JsonResponse
    {
        $comments = $commentsRepository->findBy([
            "article" => $article
        ], orderBy: ["id" => "DESC"], limit: 10, offset: $firstResult);
        $data = [];
        foreach ($comments as $comment) {
            $data[] = $this->serializeData($comment);
        }
        return $this->json($data);
    }

    private function serializeData(Comments $comment): array
    {
        $serializer = new Serializer([new ObjectNormalizer()]);
        $data = $serializer->normalize($comment, null, [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'contenu',
                'user' => ['photo', 'nom', 'prenom', 'username'], 'article' => ['id']
            ]
        ]);
        $data["date"] = $comment->getCreatedAt()->format("Y-m-d H:i:s");

        return $data;
    }
}
