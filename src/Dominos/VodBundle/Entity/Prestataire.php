<?php

namespace Dominos\VodBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prestataire
 *
 * @ORM\Table(name="vod_prestataire")
 * @ORM\Entity(repositoryClass="Dominos\VodBundle\Repository\PrestataireRepository")
 */
class Prestataire
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idpresta", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nompresta", type="string", length=255)
     */
    private $nompresta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startpresta", type="datetime")
     */
    private $startpresta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endpresta", type="datetime")
     */
    private $endpresta;

    /**
     * @ORM\OneToMany(targetEntity="Menus", mappedBy="prestataire")
     **/
    private $menus_presta;

    /**
     * @ORM\OneToMany(targetEntity="Code", mappedBy="prestataire")
     **/
    private $codes;

    /**
     * @ORM\OneToMany(targetEntity="Compteur", mappedBy="prestataire")
     **/
    private $compteurs;

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
     * Set nompresta
     *
     * @param string $nompresta
     * @return Prestataire
     */
    public function setNompresta($nompresta)
    {
        $this->nompresta = $nompresta;

        return $this;
    }

    /**
     * Get nompresta
     *
     * @return string 
     */
    public function getNompresta()
    {
        return $this->nompresta;
    }

    /**
     * Set startpresta
     *
     * @param \DateTime $startpresta
     * @return Prestataire
     */
    public function setStartpresta($startpresta)
    {
        $this->startpresta = $startpresta;

        return $this;
    }

    /**
     * Get startpresta
     *
     * @return \DateTime 
     */
    public function getStartpresta()
    {
        return $this->startpresta;
    }

    /**
     * Set endpresta
     *
     * @param \DateTime $endpresta
     * @return Prestataire
     */
    public function setEndpresta($endpresta)
    {
        $this->endpresta = $endpresta;

        return $this;
    }

    /**
     * Get endpresta
     *
     * @return \DateTime 
     */
    public function getEndpresta()
    {
        return $this->endpresta;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menus_presta = new \Doctrine\Common\Collections\ArrayCollection();
        $this->codes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->compteurs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add menus_presta
     *
     * @param \Dominos\VodBundle\Entity\Menus $menusPresta
     * @return Prestataire
     */
    public function addMenusPretum(\Dominos\VodBundle\Entity\Menus $menusPresta)
    {
        $this->menus_presta[] = $menusPresta;

        return $this;
    }

    /**
     * Remove menus_presta
     *
     * @param \Dominos\VodBundle\Entity\Menus $menusPresta
     */
    public function removeMenusPretum(\Dominos\VodBundle\Entity\Menus $menusPresta)
    {
        $this->menus_presta->removeElement($menusPresta);
    }

    /**
     * Get menus_presta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getmenusPresta()
    {
        return $this->menus_presta;
    }

    /**
     * Add codes
     *
     * @param \Dominos\VodBundle\Entity\Code $code
     * @return Prestataire
     */
    public function addCode(\Dominos\VodBundle\Entity\Code $code)
    {
        $this->codes[] = $code;

        return $this;
    }

    /**
     * Remove codes
     *
     * @param \Dominos\VodBundle\Entity\Code $code
     */
    public function removeCode(\Dominos\VodBundle\Entity\Code $code)
    {
        $this->codes->removeElement($code);
    }

    /**
     * Get codes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCodes()
    {
        return $this->codes;
    }

   /**
     * Add compteurs
     *
     * @param \Dominos\VodBundle\Entity\Compteur $compteur
     * @return Prestataire
     */
    public function addCompteur(\Dominos\VodBundle\Entity\Compteur $compteur)
    {
        $this->compteurs[] = $compteur;

        return $this;
    }

    /**
     * Remove compteur
     *
     * @param \Dominos\VodBundle\Entity\Compteur $compteur
     */
    public function removeCompteur(\Dominos\VodBundle\Entity\Compteur $compteur)
    {
        $this->compteurs->removeElement($compteur);
    }


    /**
     * Get compteurs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCompteurs()
    {
        return $this->compteurs;
    }

    public function getPrestaPeriod(){
        $date = $this->startpresta;
        $end_date = $this->endpresta;
        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($date, $interval ,$end_date);
        $dates = array();
        foreach($daterange as $date){
            $date = $date->format('Y-m-d H:i:s');
           $dates[$date] = $date;
        }
        return $dates;
    }
}