<?php

namespace App\Repository;

use App\Entity\SetType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SetType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SetType|null findOneBy(array $criteria, array $orderBy = null)
 * @method SetType[]    findAll()
 * @method SetType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SetTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SetType::class);
    }

    // /**
    //  * @return SetType[] Returns an array of SetType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SetType
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
