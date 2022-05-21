<?php

namespace App\Repository;

use App\Entity\CardDeck;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CardDeck>
 *
 * @method CardDeck|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardDeck|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardDeck[]    findAll()
 * @method CardDeck[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardDeckRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardDeck::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CardDeck $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(CardDeck $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

//    /**
//     * @return CardDeck[] Returns an array of CardDeck objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CardDeck
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
