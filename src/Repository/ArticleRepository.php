<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Galerie;
use App\Entity\Langue;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function save(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getArticlesPublishedWithoutMyArticle($user): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.readMessage", "rm")
            ->leftJoin("rm.user", "u")
            ->where('a.isPublished = :is_published')
            ->andWhere('a.user != :user')
            ->andWhere("rm.lu != :lu")
            ->setParameter("lu", false)
            ->setParameter("user", $user)
            ->setParameter('is_published', true)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllOrdered(?UserInterface $user = null, Langue $langue = null): array
    {
        $qb = $this->createQueryBuilder('a');
        if ($user) {
            $qb->where('a.user = :user')
                ->setParameter('user', $user);
        }
        if ($langue) {
            $qb->andWhere("a.langue = :langue")
                ->setParameter("langue", $langue);
        }
        return $qb->orderBy("a.updated_at", "DESC")
            ->getQuery()
            ->getResult();
    }

    public function getArticlePublished(string $categorie = null, UserInterface $user = null, Langue $langue = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect(["cat", "u", "lang"])
            ->innerJoin("a.categorie", "cat")
            ->innerJoin("a.langue", "lang")
            ->innerJoin("a.user", "u");
        $qb
            ->where('a.is_published = :published')
            ->setParameter("published", true);
        if ($categorie) {
            $qb->andWhere('cat.type = :type')
                ->setParameter('type', $categorie);
        }
        if ($user) {
            $qb->andWhere("a.user = :user")
                ->setParameter("user", $user);
        }
        if ($langue) {
            $qb->andWhere('a.langue = :langue')
                ->setParameter("langue", $langue);
        }

        $qb->orderBy('a.updated_at', 'DESC')
            ->setMaxResults(10);

        if ($categorie == "Populaire") {
            $qb->setMaxResults(5);
        } else if ($categorie == "Calendrier d'évènement") {
            $qb->setMaxResults(10);
        }
        return $qb->getQuery()
            ->getResult();
    }

    public function findByCategorieArticleWithLimit(Categorie $categorie, int $firstResult, Langue $langue): array
    {
        return $this->createQueryBuilder('a')
            ->addSelect(["u", "cat", "lang"])
            ->join("a.user", "u")
            ->join("a.categorie", "cat")
            ->join("a.langue", "lang")
            ->where('a.categorie = :categorie')
            ->andWhere('a.is_published = :is_published')
            ->andWhere('a.langue = :langue')
            ->setParameter("categorie", $categorie)
            ->setParameter('is_published', true)
            ->setParameter("langue", $langue)
            ->orderBy("a.updated_at", "DESC")
            ->setFirstResult($firstResult)
            ->setMaxResults(10)
            ->getQuery()
            ->getArrayResult();
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function getAllArticleByType($type): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $type)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
