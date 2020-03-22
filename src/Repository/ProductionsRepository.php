<?php

namespace App\Repository;

use App\Entity\Productions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Productions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Productions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Productions[]    findAll()
 * @method Productions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Productions::class);
    }

    public function getAllEmployee($idProject): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('u')
            ->innerJoin('p.user', 'u')
            ->select('
                sum(p.hour_number) as total_hour, 
                sum(p.total_cost) as total_cost, 
                u.name, 
                u.first_name
                ')
            ->where('p.project = :id')
            ->setParameter('id', $idProject)
            ->groupBy('u.id')
            ->orderBy('total_cost', "DESC")
            ->getQuery()
            ->getResult();
    }

    public function getLastProds(): array
    {
        return $this->createQueryBuilder('prod')
            ->addSelect('u')
            ->innerJoin('prod.user', 'u')
            ->addSelect('pr')
            ->innerJoin('prod.project', 'pr')
            ->groupBy('prod.id')
            ->orderBy('prod.creation_date', "DESC")
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getNbrHourProd(): array
    {
        return $this->createQueryBuilder('prod')
            ->select("sum(prod.hour_number) as nbrProd")
            ->getQuery()
            ->getResult();
    }

    public function getBestEmployee(): array
    {
        return $this->createQueryBuilder("pr")
            ->addSelect("u")
            ->innerJoin("pr.user", "u")
            ->select("
                sum(pr.total_cost) as total,
                u.name,
                u.first_name,
                u.hiring_date
                ")
            ->groupBy("u.id")
            ->orderBy("total", "DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }


    // /**
    //  * @return Productions[] Returns an array of Productions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Productions
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
