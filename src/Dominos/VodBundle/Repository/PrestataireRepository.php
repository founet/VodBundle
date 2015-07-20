<?php

namespace Dominos\VodBundle\Repository ;

use Doctrine\ORM\EntityRepository;
use Dominos\VodBundle\Entity\Prestataire;

class PrestataireRepository extends EntityRepository {

	public function checkPrestaPeriod(Prestataire $prestataire){
		$qb = $this->_em->createQueryBuilder()
			->select('count(p)')
			->from($this->_entityName,'p')
			->where('p.startpresta < :enddate')
	       	->setParameter('enddate', $prestataire->getEndpresta())
	     	->andWhere('p.endpresta >= :startdate')
	       	->setParameter('startdate', $prestataire->getStartpresta());
		    if($prestataire->getId()){
			    $qb->andWhere('p.id <> :idpresta');
			    $qb->setParameter('idpresta',$prestataire->getId());
		    }

		$count = $qb->getQuery()->getSingleScalarResult();
		return $count;
	}

	public function getPrestataires(){
		$qb = $this->createQueryBuilder('p');
        $qb->where('p.endpresta >= :now')
			->setParameter('now', new \DateTime())
            ->orderBy('p.startpresta');

        return $qb->getQuery()->getResult();
	}


	
}