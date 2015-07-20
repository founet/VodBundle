<?php

namespace Dominos\VodBundle\Repository ;

use Doctrine\ORM\EntityRepository;
use Dominos\VodBundle\Entity\Prestataire;
use Dominos\VodBundle\Entity\Compteur;

class MenusRepository extends EntityRepository {

	public function getMenusMagByPresta($idpresta,$idmag){

		$qb = $this->_em->createQueryBuilder()
			->select('m')
			->from($this->_entityName,'m')
			->leftJoin('m.prestataire', 'p')
			->leftJoin('p.compteurs', 'c')
			->where('p.id = :idpresta')
	       	->setParameter('idpresta', $idpresta)
	     	->andWhere('m.magnum = :magnum')
	       	->setParameter('magnum', $idmag)
	       	->andWhere('c.datepresta = :datepresta')
	       	->setParameter('datepresta', date('Y-m-d'));
	
		return $qb->getQuery()->getOneOrNullResult();

	}
	
}