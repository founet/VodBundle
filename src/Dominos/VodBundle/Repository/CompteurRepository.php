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
	       	->setParameter('datepresta', $compteur->getDatepresta());

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

	public function getIsOperation($nbreCouponTemp){
		$today = date('d-m-Y').' 23:59:59';
		$qb = $this->_em->createQueryBuilder()
			->select('c')
			->from($this->_entityName,'c')
			->where('c.datepresta = :datepresta')
	       	->setParameter('datepresta', $today)
       		->andWhere('(c.nbrecodeday - c.nbrecodeused) > :nbrecoupons')
	       	->setParameter('nbrecoupons', $nbreCouponTemp);

		$result = $qb->getQuery()->getOneOrNullResult();
		return $result;
	}

	public function findByPresta($prestataire){
		$qb = $this->_em->createQueryBuilder()
			->select("STR_TO_DATE(c.datepresta,'%d-%m-%Y %T') datep,c.nbrecodeday,c.nbrecodeused,c.id")
			->from($this->_entityName,'c')
			->where('c.prestataire = :prestataire')
	       	->setParameter('prestataire', $prestataire)
	       	->orderby('datep','ASC');

       	$result = $qb->getQuery()->getResult();
		return $result;
	}

}