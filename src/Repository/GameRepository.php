<?php

namespace App\Repository;

use Doctrine\ORM\NonUniqueResultException;
use App\Entity\{Game, GameBuffer};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    /**
     * GameRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * Find Game By GameBuffer and period
     *
     * @throws NonUniqueResultException
     */
    public function findByBuffer(GameBuffer $buffer, \DateTimeInterface $dateStart, \DateTimeInterface $dateEnd): ?Game
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.language = :lang')
            ->andWhere('g.league = :league')
            ->andWhere('g.team1 = :team1')
            ->andWhere('g.team2 = :team2')
            ->andWhere('g.date >= :dateStart AND g.date <= :dateEnd')
            ->setParameter('lang', $buffer->getLanguage())
            ->setParameter('league', $buffer->getLeague())
            ->setParameter('team1', $buffer->getTeam1())
            ->setParameter('team2', $buffer->getTeam2())
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd)
            ->orderBy('g.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Get random Game
     *
     * @throws NonUniqueResultException
     */
    public function getRandom(): ?Game
    {
        return $this->createQueryBuilder('g')
            ->addOrderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // /**
    //  * @return Game[] Returns an array of Game objects
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
    public function findOneBySomeField($value): ?Game
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