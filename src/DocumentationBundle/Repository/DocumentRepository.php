<?php

namespace DocumentationBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * DocumentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DocumentRepository extends EntityRepository
{

    public function getDocByCatagorie()
    {
        return $this->createQueryBuilder('document')
            ->groupBy('document.categorie');
    }

    public function getDocumentsByDestinataire($destinataire)
    {
        return $this->createQueryBuilder('document')
            ->where('document.destinataire = :destinataire')
            ->setParameter('destinataire', $destinataire)
            ->getQuery()
            ->execute()
            ;
    }
}
