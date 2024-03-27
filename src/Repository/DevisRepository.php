<?php

namespace App\Repository;

use App\Entity\Devis;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Devis>
 *
 * @method Devis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devis[]    findAll()
 * @method Devis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devis::class);
    }

    //    /**
    //     * @return Devis[] Returns an array of Devis objects
    //     */
    public function findByUserWithDetails(User $user): array
    {
        return $this->createQueryBuilder('d')
            ->select('d', 'u', 't')
            ->leftJoin('d.User', 'u') // Jointure sur l'entité User
            ->leftJoin('d.typeOperation', 't') // Jointure sur l'entité TypeOperation
            ->andWhere('d.User = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Devis
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
