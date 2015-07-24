<?php

namespace Dominos\VodBundle\Repository ;

use Doctrine\ORM\EntityRepository;
use Dominos\VodBundle\Entity\Prestataire;

class CodeRepository extends EntityRepository {

	public function getOneCodeByPresta($idpresta){
		$today = date('d-m-Y').' 23:59:59';
		$qb = $this->_em->createQueryBuilder()
			->select('c')
			->from($this->_entityName,'c')
			->leftJoin('c.prestataire', 'p')
			->leftJoin('p.compteurs', 'compteur')
	       	->where('p.id = :idpresta')
	       	->setParameter('idpresta', $idpresta)
       		->andWhere('compteur.datepresta = :today')
	       	->setParameter('today', $today)
	       	->andWhere('c.dateused IS NULL AND c.datetemp IS NULL')
	       	->andWhere('(compteur.nbrecodeday - compteur.nbrecodeused) > 0')
	     	->setMaxResults(1);

		$result = $qb->getQuery()->getOneOrNullresult();
		return $result;
	}
	// Réinitialisation des codes démandés non grillés
	public function RAZDateTemp(){

		$qb = $this->_em->createQueryBuilder()
			->update($this->_entityName,'c')
			->set('c.datetemp', 'NULL')
	       	->where('c.datetemp IS NOT NULL')
       		->andWhere("DATE_FORMAT(c.datetemp,'%Y %M %d') < DATE_FORMAT(CURRENT_DATE(),'%Y %M %d')")
	       	->andWhere('c.dateused IS NULL');
	       
		$result = $qb->getQuery()->execute();
		return $result;
	}
	
}