<?php

namespace Dominos\VodBundle\Repository ;

use Doctrine\ORM\EntityRepository;
use Dominos\VodBundle\Entity\Prestataire;

class CodeRepository extends EntityRepository {

	public function NbreTotalDispo(Prestataire $prestataire){
		$qb = $this->_em->createQueryBuilder()
			->select('count(c)')
			->from($this->_entityName,'c')
	       	->where('c.prestataire = :prestataire')
	       	->setParameter('prestataire', $prestataire);

		$result = $qb->getQuery()->getSingleScalarResult();
		return $result;
	}

	public function NbreTotalNonGrilles(Prestataire $prestataire){
		$qb = $this->_em->createQueryBuilder()
			->select('sum(c)')
			->from($this->_entityName,'c')
	       	->where('c.prestataire = :prestataire')
	       	->setParameter('prestataire', $prestataire)
	       	->andWhere('c.dateused IS NULL');
	     
		$result = $qb->getQuery()->getSingleScalarResult();
		return $result;
	}
	
}