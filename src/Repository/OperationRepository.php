<?php

namespace App\Repository;

use App\Entity\Operation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Operation>
 *
 * @method Operation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Operation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Operation[]    findAll()
 * @method Operation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operation::class);
    }

    //    /**
    //     * @return Operation[] Returns an array of Operation objects
    //     */
    public function findByWithDevis(array $criteria): array
    {
        return $this->createQueryBuilder('o')
            ->select('o', 'd') // Sélectionnez à la fois l'opération et le devis associé
            ->join('o.devis', 'd') // Jointure sur l'entité Devis
            ->andWhere('o.user = :user')
            ->setParameter('user', $criteria['user'])
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Operation
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
