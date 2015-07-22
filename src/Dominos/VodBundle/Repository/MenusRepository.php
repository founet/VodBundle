<?php

namespace Dominos\VodBundle\Repository ;

use Doctrine\ORM\EntityRepository;
use Dominos\VodBundle\Entity\Prestataire;
use Dominos\VodBundle\Entity\Compteur;

class MenusRepository extends EntityRepository {

	public function getMenusMagByPresta($idmag){
		$today = date('d-m-Y').' 23:59:59';
		$qb = $this->_em->createQueryBuilder()
			->select('m')
			->from($this->_entityName,'m')
			->leftJoin('m.prestataire', 'p')
			->leftJoin('p.compteurs', 'c')
	     	->andWhere('m.magnum = :magnum')
	       	->setParameter('magnum', $idmag)
	       	->andWhere('c.datepresta = :today')
	       	->setParameter('today', $today)
	       	->andWhere('(c.nbrecodeday - c.nbrecodeused) > 0');
	
		return $qb->getQuery()->getOneOrNullresult();

	}
	
}