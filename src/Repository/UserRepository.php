<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    public function findByEmailOrUsername(User $user): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->orWhere("u.email = :email")
            ->setParameter('username', $user->getUsername())
            ->setParameter('email', $user->getEmail())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function loadUserByIdentifier(string $usernameOrEmail): ?User
    {
        return $this->createQueryBuilder("u")
            ->where('u.username = :userOrEmail')
            ->orWhere('u.email = :userOrEmail')
            //->andWhere('u.isActif = :actif')
            ->setParameter('userOrEmail', $usernameOrEmail)
            //->setParameter('actif', true)
            ->getQuery()
            ->getOneOrNullResult();
        // $entityManager = $this->getEntityManager();

        // return $entityManager->createQuery(
        //     'SELECT u
        //         FROM App\Entity\User u
        //         WHERE u.username = :query
        //         OR u.email = :query
        //         AND u.isActif = :actif'
        // )

    }

    public function getAllWithoutMy(string $email): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.email != :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getArrayResult();
    }

    public function getUsersNotWriteArticle(array $user_write_article = null): array
    {
        $qb = $this->createQueryBuilder('u');
        return $qb->where($qb->expr()->notIn("u.id", ":list_user"))
            ->setParameter('list_user', $user_write_article)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

       public function findAllOrdered(): array
       {
           return $this->createQueryBuilder('u')
                      ->orderBy("u.id", "ASC")
                    ->getQuery()
                    ->getResult()
           ;
       }
}