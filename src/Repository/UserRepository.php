<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function getAllWithJob(): Query
    {
        return $this->createQueryBuilder('u')
            ->addSelect('j')
            ->leftJoin('u.job', 'j')
            ->where('j.id = u.job')
            ->orderBy('u.name', "ASC")
            ->getQuery();
    }

    public function getOneWithJob($id): array
    {
        return $this->createQueryBuilder('u')
            ->addSelect('j')
            ->leftJoin('u.job', 'j')
            ->where('j.id = u.job')
            ->andWhere('u.id = :id')
            ->setParameter("id", $id)
            ->getQuery()
            ->getResult();
    }

    public function getDetailsUser($id): array
    {
        return $this->createQueryBuilder("u")
            ->addSelect('j')
            ->leftJoin('u.job', 'j')
            ->addSelect('p')
            ->leftJoin("u.productions", 'p')
            ->orderBy("p.creation_date", "DESC")
            ->addSelect('pr')
            ->leftJoin("p.project", "pr")
            ->where("u.id = :id")
            ->setParameter("id", $id)
            ->getQuery()
            ->getResult();
    }

    public function countEmployee(): array
    {
        return $this->createQueryBuilder('u')
            ->select("count(u.id) as nbr_employee")
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
