<?php

namespace App\Repository;

use App\Entity\Legality;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Legality|null find($id, $lockMode = null, $lockVersion = null)
 * @method Legality|null findOneBy(array $criteria, array $orderBy = null)
 * @method Legality[]    findAll()
 * @method Legality[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegalityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Legality::class);
    }

    // /**
    //  * @return Legality[] Returns an array of Legality objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Legality
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
