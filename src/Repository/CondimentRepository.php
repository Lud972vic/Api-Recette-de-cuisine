<?php

namespace App\Repository;

use App\Entity\Condiment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Condiment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Condiment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Condiment[]    findAll()
 * @method Condiment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CondimentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Condiment::class);
    }

    // /**
    //  * @return Condiment[] Returns an array of Condiment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Condiment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
