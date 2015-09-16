<?php

namespace Dominos\VodBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Compteur
 *
 * @ORM\Table(name="vod_compteur")
 * @ORM\Entity(repositoryClass="Dominos\VodBundle\Repository\CompteurRepository")
 */
class Compteur
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idcompteur", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

     /**
     * @ORM\ManyToOne(targetEntity="Prestataire", inversedBy="compteurs")
     * @ORM\JoinColumn(name="idpresta", referencedColumnName="idpresta")
     **/
    private $prestataire;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datepresta", type="string", nullable= false)
     */
    private $datepresta;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbrecodeday", type="integer")
     */
    private $nbrecodeday;

    /**
     * @var interger
     *
     * @ORM\Column(name="nbrecodeused", type="integer")
     */
    private $nbrecodeused = 0;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set datepresta
     *
     * @param \DateTime $datepresta
     * @return Code
     */
    public function setDatepresta($datepresta)
    {
        $this->datepresta = $datepresta;

        return $this;
    }

     /**
     * Get datepresta
     *
     * @return \DateTime 
     */
    public function getDatepresta()
    {
        return $this->datepresta;
    }

    /**
     * Set nbrecodeday
     *
     * @param integer $nbrecodeday
     * @return Compteur
     */
    public function setNbrecodeday($nbrecodeday)
    {
        $this->nbrecodeday = $nbrecodeday;

        return $this;
    }

    /**
     * Get nbrecodeday
     *
     * @return integer 
     */
    public function getNbrecodeday()
    {
        return $this->nbrecodeday;
    }

    /**
     * Set nbrecodeused
     *
     * @param string $nbrecodeused
     * @return Compteur
     */
    public function setNbrecodeused($nbrecodeused)
    {
        $this->nbrecodeused = $nbrecodeused;

        return $this;
    }

    /**
     * Get nbrecodeused
     *
     * @return string 
     */
    public function getNbrecodeused()
    {
        return $this->nbrecodeused;
    }

    /**
     * Set prestataire
     *
     * @param \Dominos\VodBundle\Entity\Prestaire $prestataire
     * @return Compteur
     */
    public function setPrestataire(\Dominos\VodBundle\Entity\Prestataire $prestataire = null)
    {
        $this->prestataire = $prestataire;

        return $this;
    }

    /**
     * Get prestataire
     *
     * @return \Dominos\VodBundle\Entity\Prestaire 
     */
    public function getPrestataire()
    {
        return $this->prestataire;
    }
    
    public function getNbreCodeRestants(){
        return  $this->nbrecodeday - $this->nbrecodeused;
    }



}
