<?php

namespace App\Repository;

use App\Entity\{League, Sport};
use App\Utils\Util;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @method League|null find($id, $lockMode = null, $lockVersion = null)
 * @method League|null findOneBy(array $criteria, array $orderBy = null)
 * @method League[]    findAll()
 * @method League[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeagueRepository extends ServiceEntityRepository
{
    /**
     * LeagueRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, League::class);
    }

    /**
     * Find Leagues by Pairs
     *
     * @param array $params
     *
     * @return League[]
     */
    public function findByPair(array $params)
    {
        $stringParams = Util::arrayToString($params);

        $sport = $this->getEntityManager()
            ->getRepository(Sport::class);

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult($this->_entityName, 'l');
        $rsm->addFieldResult('l','id','id');
        $rsm->addFieldResult('l','name','name');
        $rsm->addMetaResult('l','sport_id','sport_id');

        return $this->getEntityManager()
            ->createNativeQuery("
                SELECT l.id, l.name, l.sport_id
                FROM                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
                  {$this->getClassMetadata()->getTableName()} l
                LEFT JOIN {$sport->getClassMetadata()->getTableName()} s 
                    ON s.id = l.sport_id                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
                WHERE 
                    (l.name, s.name) IN ({$stringParams})
            ", $rsm)
            ->getResult();
    }

    // /**
    //  * @return League[] Returns an array of League objects
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
    public function findOneBySomeField($value): ?League
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