<?php

namespace AppBundle\Repository;

use UserBundle\Entity\User;

/**
 * TicketRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TicketRepository extends \Doctrine\ORM\EntityRepository
{
    public function findTicketsByAuthor(User $user)
    {
        return $this->createQueryBuilder('ticket')
            ->where('ticket.statut = :statut')
            ->setParameter('statut', 1)
            ->andWhere('ticket.fromWho = :user')
            ->setParameter('user', $user)
            ;
    }
}