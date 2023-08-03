<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Galerie;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Repository\VisitorRepository;
use App\Service\ArticleService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;


#[IsGranted("ROLE_REDACTEUR")]
class AdminController extends AbstractController
{
    public function __construct(private CacheInterface $cache)
    {
    }
    #[Route('/{_locale}/admin/dashboard', name: 'app_dashboard', requirements: ['_locale' => 'en|fr|mg'], defaults: ['_locale' => 'fr'])]
    public function dashboard(Request $request, VisitorRepository $visitorRep, ArticleService $articleService): Response
    {
        $articlesWriteByUser = $articleService->getArticlesByUser();

        $dates = [
            (new \DateTime())->sub(new \DateInterval("P5D"))->format("Y-m-d"),
            (new \DateTime())->format("Y-m-d")
        ];

        $labels = [];
        $data = [];

        $form = $this->createFormBuilder()
            ->add('dates', ChoiceType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $dates = $form->get('dates')->getData();
            $dates = explode(" - ", $dates);
            $dates = [$dates[0], $dates[1]];
        }

        // $labels = intervalDate($dates);
        // $labels = CreateIntervalDate($dates);


        $debut_jour = explode('-', $dates[0])[2];
        $debut_mois = explode('-', $dates[0])[1];
        $debut_annee = explode('-', $dates[0])[0];

        $fin_jour = explode('-', $dates[1])[2];
        $fin_mois = explode('-', $dates[1])[1];
        $fin_annee = explode('-', $dates[1])[0];

        $debut_date = mktime(0, 0, 0, $debut_mois, $debut_jour, $debut_annee);
        $fin_date = mktime(0, 0, 0, $fin_mois, $fin_jour, $fin_annee);


        for ($i = $debut_date; $i <= $fin_date; $i += 86400) {
            $labels[] = date("Y-m-d", $i);
        }
        $visitors = $this->cache->get("visitor_view", function (ItemInterface $item) use ($visitorRep, $dates) {
            $item->expiresAfter(60);
            return $visitorRep->getVisitor($dates);
        });

        /**
         * creation de data par jour s'il n'existe pas
         */
        foreach ($labels as $label) {
            foreach ($visitors as $visitor) {
                if (!array_key_exists($label, $data)) {
                    if ($visitor["visited_at"]->format("Y-m-d") == $label) {
                        $data[$label] = $visitor["nb_visitor"];
                    } else {
                        $data[$label] = 0;
                    }
                } else {
                    if ($visitor["visited_at"]->format("Y-m-d") == $label) {
                        $data[$label] = $visitor["nb_visitor"];
                    }
                }
            }
        }

        return $this->render('admin/dashboard.html.twig', [
            'visitors' => json_encode($data),
            "form" => $form->createView(),
            "usersWithArticles" => $articlesWriteByUser
        ]);
    }

    #[Route('/{_locale}/admin/article/{article_get}', name: 'app_article', requirements: ['_locale' => 'en|fr|mg'], defaults: ['_locale' => 'fr'])]
    public function article(
        ?Article $article_get = null,
        Request $request,
        EntityManagerInterface $em,
        ArticleRepository $articleRepository,
        UserRepository $userRepository,
        SessionInterface $session
    ): Response {
        $articles = [];
        $article = new Article();
        $option = null;
        $update = "";
        $galerie_exist = false;
        $categorie = null;

        if ($article_get) {
            $article = $article_get;
            $option = $request->query->get('option');
            if ($option == "delete") {
                if ($article->getCategorie()->getType() == "Récent") {
                    $session->set('idArticleToRemoveInArticleRecent', $article->getId());
                }
                $em->remove($article);
                $em->flush();
                return $this->redirectToRoute("app_article");
            } else if ($option == "publish" or $option == "unPublish") {
                if ($article->getCategorie()->getType() == "Récent") {
                    $session->set('idArticleToRemoveInArticleRecent', $article->getId());
                }
                if ($option == "publish") {
                    $session->set('idArticleToRemoveInArticleRecent', null);
                    $article->setIsPublished(true)
                        ->setUpdatedAt(new DateTimeImmutable());
                } else {
                    $session->set('idArticleToRemoveInArticleRecent', $article->getId());
                    $article->setIsPublished(false);
                }
                $em->flush();

                return $this->redirectToRoute("app_article");
            } else if ($option == "update") {
                $session->set('idArticleToRemoveInArticleRecent', null);
                $categorie = $article->getCategorie()->getType();
                $update = $option;

                if ($article->getCategorie()->getType() == "Populaire") {
                    $galerie_exist = true;
                }
            }
        }

        $form = $this->createForm(
            ArticleType::class,
            $article,
            [
                "date_event" => $article->getEventAt() ? $article->getEventAt()->format("Y-m-d H:i:s") : null,
                "galerie_exist" => $galerie_exist
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** UploadedFile $image */
            $image = $form->get('image_article')->getData();
            $galeries = $form->get('galerie_pop')->getData();
            /**
             * event_at pour calendrier d'évènement
             */
            if ($article->getCategorie()->getType() == "Calendrier d'évènement") {
                $event_at = $form->get('event_at')->getData();
                $dates = explode(' ', $event_at);
                $hours_min = explode(":", $dates[1]);
                $hours = $hours_min[0];
                $min = $hours_min[1];

                $date_event = new DateTimeImmutable($event_at);
                $date_event->setTime($hours, $min);

                $article->setEventAt(new DateTimeImmutable($event_at));
            }
            /**
             * les utilisateurs qui n'ont pas encore lu les articles
             */
            $users = $userRepository->findAll();
            $ids = [];

            foreach ($users as $user) {
                $ids[] = $user->getId();
            }

            if ($image) {
                $extension = $image->getClientOriginalExtension();
                $name_image = uniqid() . '.' . $extension;
                if ($image->move($this->getParameter('app_dir_article'), $name_image)) {
                    $article->setImage($name_image);
                }
            }
            if ($galeries) {
                $max_galerie_image = 8;
                /**
                 * update article populaire
                 * on enleve tous les galeries correspondant auparavant
                 */
                if ($option) {
                    $galeries_to_remove = $article->getGalerie();
                    foreach ($galeries_to_remove as $gale) {
                        $article->removeGalerie($gale);
                    }
                    $em->flush();
                }
                if (count($galeries) <= $max_galerie_image) {
                    for ($i = 0; $i < count($galeries); $i++) {
                        $extension = $galeries[$i]->getClientOriginalExtension();
                        $name_image = uniqid() . '.' . $extension;
                        if ($galeries[$i]->move($this->getParameter('app_dir_galerie'), $name_image)) {
                            $gal = new Galerie();
                            $gal->setNomImage($name_image);
                            $article->addGalerie($gal);
                        }
                    }
                } else {
                    $this->addFlash('error', "Vous ne pouvez qu'uploader " . $max_galerie_image . " images au maximum");
                    return $this->redirectToRoute('app_article');
                }
            }
            $article
                ->setUser($this->getUser())
                ->setIdsUnreadNotification($ids);

            if ($option) {
                $article->setUpdatedAt(new \DateTimeImmutable());
            }
            /**
             * si insertion
             */
            if (is_null($option)) {
                $em->persist($article);
            }
            $em->flush();
            return $this->redirectToRoute("app_article");
        }

        $articles = $articleRepository->findAllOrdered();

        return $this->render('admin/article.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles,
            'update' => $update,
            "categorie" => $categorie
        ]);
    }
}
