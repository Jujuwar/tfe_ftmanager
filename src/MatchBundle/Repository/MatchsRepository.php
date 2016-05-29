<?php

namespace MatchBundle\Repository;

/**
 * MatchsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MatchsRepository extends \Doctrine\ORM\EntityRepository
{
    function findByTeam( $id ) {
        $qb = $this->createQueryBuilder( 'm' );

        $qb->where( $qb->expr()->orX()
            ->add( 'm.team1 = ?1' )
            ->add( 'm.team2 = ?2' )
        );

        $qb->setParameter( '1', $id );
        $qb->setParameter( '2', $id );

        return $qb->getQuery()->getResult();
    }
}
