<?php

namespace Dominos\VodBundle\Repository ;

use Doctrine\ORM\EntityRepository;
use Dominos\VodBundle\Entity\Prestataire;

class CodeRepository extends EntityRepository {

	public function getOneCodeByPresta($idpresta){
		$qb = $this->_em->createQueryBuilder()
			->select('c')
			->from($this->_entityName,'c')
			->leftJoin('c.prestataire', 'p')
			->leftJoin('p.compteurs', 'compteur')
	       	->where('p.id = :idpresta')
	       	->setParameter('idpresta', $idpresta)
	       	->andWhere('c.dateused IS NULL')
	       	->andWhere('(compteur.nbrecodeday - compteur.nbrecodeused) > 0')
	     	->setMaxResults(1);

		$result = $qb->getQuery()->getOneOrNullResult();
		return $result;
	}
	
}