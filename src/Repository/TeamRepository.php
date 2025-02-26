<?php

namespace App\Repository;

use App\Entity\{Team, Sport};
use App\Utils\Util;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    /**
     * TeamRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /**
     * Find Teams by Pairs
     *
     * @return Team[]
     */
    public function findByPair(array $params): array
    {
        $stringParams = Util::arrayToString($params);

        $sport = $this->getEntityManager()
            ->getRepository(Sport::class);

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult($this->_entityName, 't');
        $rsm->addFieldResult('t','id','id');
        $rsm->addFieldResult('t','name','name');
        $rsm->addMetaResult('t','sport_id','sport_id');

        return $this->getEntityManager()
            ->createNativeQuery("
                SELECT t.id, t.name, t.sport_id
                FROM                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
                    {$this->getClassMetadata()->getTableName()} t
                LEFT JOIN {$sport->getClassMetadata()->getTableName()} s 
                    ON s.id = t.sport_id                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
                WHERE 
                    (t.name, s.name) IN ($stringParams)
                ", $rsm)
            ->getResult();
    }

    // /**
    //  * @return Team[] Returns an array of Team objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Team
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}