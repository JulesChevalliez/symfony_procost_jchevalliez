<?php

namespace App\Repository;

use App\Entity\Projects;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Projects|null find($id, $lockMode = null, $lockVersion = null)
 * @method Projects|null findOneBy(array $criteria, array $orderBy = null)
 * @method Projects[]    findAll()
 * @method Projects[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projects::class);
    }


    public function findAllQuery(): Query
    {
        return $this->createQueryBuilder('pr')
            ->getQuery();
    }

    public function getProjectWithNbrEmployee($id): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('pr')
            ->leftJoin('p.productions', 'pr')
            ->select("
                count(distinct pr.user) as nbr_employee,
                p.id,
                p.name,
                p.description,
                p.selling_price,
                p.creation_date,
                p.delivery_date,
                p.total_cost,
                p.delivered
            ")
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->groupBy('p.id')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function getAllFinishedProject(): array
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.delivered = 1')
            ->getQuery()
            ->getResult();
    }

    public function getNbrFinished(): array
    {

        return $this->createQueryBuilder('pr')
            ->addSelect("sum(case when pr.delivered like '1' then '1' else '0' end) as finished")
            ->addSelect("sum(case when pr.delivered like '0' then '1' else '0' end) as inProgress")
            ->getQuery()
            ->getResult();
    }

    public function getLastProjects(): array
    {
        return $this->createQueryBuilder('pr')
            ->orderBy('pr.creation_date', "DESC")
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }


    public function getNbrWorthProject(): array
    {
        return $this->createQueryBuilder("pr")
            ->select("count(pr.id) as nbrWorthProject")
            ->where("pr.total_cost < pr.selling_price")
            ->orWhere("pr.total_cost is null")
            ->getQuery()
            ->getResult();
    }
//    public function getAllUnfinishedProject(): Query
//    {
//        return $this->createQueryBuilder('pr')
//            ->where('pr.delivered = 0')
//            ->getQuery();
//    }

    // /**
    //  * @return Projects[] Returns an array of Projects objects
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
    public function findOneBySomeField($value): ?Projects
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
