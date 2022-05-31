<?php

namespace App\Repository;

use App\Entity\VMUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VMUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method VMUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method VMUser[]    findAll()
 * @method VMUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VMUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VMUser::class);
    }

    // /**
    //  * @return VMUser[] Returns an array of VMUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VMUser
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
