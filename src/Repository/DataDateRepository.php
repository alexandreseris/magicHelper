<?php

namespace App\Repository;

use App\Entity\DataDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DataDate|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataDate|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataDate[]    findAll()
 * @method DataDate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataDateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataDate::class);
    }

    // /**
    //  * @return DataDate[] Returns an array of DataDate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DataDate
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
