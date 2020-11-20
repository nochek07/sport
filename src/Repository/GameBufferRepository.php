<?php

namespace App\Repository;

use App\Entity\{Game, GameBuffer};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GameBuffer|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameBuffer|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameBuffer[]    findAll()
 * @method GameBuffer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameBufferRepository extends ServiceEntityRepository
{
    /**
     * GameBufferRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameBuffer::class);
    }

    /**
     * Find GameBuffer by Game
     * Find GameBuffer by Game and filter
     *
     * @param Game $game
     * @param array $filter
     *
     * @return GameBuffer[]
     */
    public function findByGame(Game $game, array $filter = [])
    {
        $builder = $this->createQueryBuilder('gb')
            ->andWhere('gb.game = :game')
            ->setParameter('game', $game);

        if (isset($filter['source'])) {
            $builder
                ->leftJoin('App\Entity\Source', 's', 'WITH', 'gb.source = s')
                ->andWhere('s.name = :source')
                ->setParameter('source', $filter['source'])
            ;
        }

        if (isset($filter['start'])) {
            $builder
                ->andWhere('gb.date >= :start AND gb.date <= :end')
                ->setParameter('start', new \DateTime($filter['start']))
                ->setParameter('end', new \DateTime($filter['end']))
            ;
        }

        return $builder->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return GameBuffer[] Returns an array of GameBuffer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GameBuffer
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}