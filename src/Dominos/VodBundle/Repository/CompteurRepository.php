<?php

namespace Dominos\VodBundle\Repository ;

use Doctrine\ORM\EntityRepository;
use Dominos\VodBundle\Entity\Prestataire;
use Dominos\VodBundle\Entity\Compteur;

class CompteurRepository extends EntityRepository {

	public function NbrePrestaByDay(Compteur $compteur){
		$qb = $this->_em->createQueryBuilder()
			->select('count(c)')
			->from($this->_entityName,'c')
			->where('c.datepresta = :datepresta')
			->andWhere('c.prestataire = :prestataire')
	       	->setParameter('datepresta', $compteur->getDatepresta())
	       	->setParameter('prestataire', $compteur->getPrestataire());

		$result = $qb->getQuery()->getSingleScalarResult();
		return $result;
	}
	
	public function NbreCodesNotUsed(Compteur $compteur){
		$qb = $this->_em->createQueryBuilder()
			->select('COALESCE(sum(c.nbrecodeday - c.nbrecodeused),0)')
			->from($this->_entityName,'c')
			->where('c.datepresta < :datepresta')
			->andWhere('c.prestataire = :prestataire')
			->andWhere('c.datepresta < :now')
	       	->setParameter('datepresta', $compteur->getDatepresta())
	       	->setParameter('now', new \DateTime())
	       	->setParameter('prestataire', $compteur->getPrestataire());

		$result = $qb->getQuery()->getSingleScalarResult();
		return $result;
	}

	public function NbreCodesVentillesByPresta($prestataire){
		$qb = $this->_em->createQueryBuilder()
			->select('COALESCE(sum(c.nbrecodeday),0)')
			->from($this->_entityName,'c')
			->where('c.prestataire = :prestataire')
	       	->setParameter('prestataire', $prestataire);

		$result = $qb->getQuery()->getSingleScalarResult();
		return $result;
	}

}