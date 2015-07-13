<?php

namespace Dominos\VodBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menus
 *
 * @ORM\Table(name="vod_menus")
 * @ORM\Entity
 */
class Menus
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idmenus", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="magnum", type="string", length=255)
     */
    private $magnum;

     /**
     * @ORM\ManyToOne(targetEntity="Prestataire", inversedBy="menus_presta")
     * @ORM\JoinColumn(name="idpresta", referencedColumnName="idpresta")
     **/
    private $prestataire;

    /**
     * @var string
     *
     * @ORM\Column(name="menu1", type="string", length=255)
     */
    private $menu1;

    /**
     * @var string
     *
     * @ORM\Column(name="menu2", type="string", length=255)
     */
    private $menu2;

    /**
     * @var string
     *
     * @ORM\Column(name="menu3", type="string", length=255)
     */
    private $menu3;

    /**
     * @var string
     *
     * @ORM\Column(name="menu4", type="string", length=255)
     */
    private $menu4;


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
     * Set magnum
     *
     * @param string $magnum
     * @return Menus
     */
    public function setMagnum($magnum)
    {
        $this->magnum = $magnum;

        return $this;
    }

    /**
     * Get magnum
     *
     * @return string 
     */
    public function getMagnum()
    {
        return $this->magnum;
    }

    /**
     * Set menu1
     *
     * @param string $menu1
     * @return Menus
     */
    public function setMenu1($menu1)
    {
        $this->menu1 = $menu1;

        return $this;
    }

    /**
     * Get menu1
     *
     * @return string 
     */
    public function getMenu1()
    {
        return $this->menu1;
    }

    /**
     * Set menu2
     *
     * @param string $menu2
     * @return Menus
     */
    public function setMenu2($menu2)
    {
        $this->menu2 = $menu2;

        return $this;
    }

    /**
     * Get menu2
     *
     * @return string 
     */
    public function getMenu2()
    {
        return $this->menu2;
    }

    /**
     * Set menu3
     *
     * @param string $menu3
     * @return Menus
     */
    public function setMenu3($menu3)
    {
        $this->menu3 = $menu3;

        return $this;
    }

    /**
     * Get menu3
     *
     * @return string 
     */
    public function getMenu3()
    {
        return $this->menu3;
    }

    /**
     * Set menu4
     *
     * @param string $menu4
     * @return Menus
     */
    public function setMenu4($menu4)
    {
        $this->menu4 = $menu4;

        return $this;
    }

    /**
     * Get menu4
     *
     * @return string 
     */
    public function getMenu4()
    {
        return $this->menu4;
    }

    /**
     * Set prestataire
     *
     * @param \Dominos\VodBundle\Entity\Prestaire $prestataire
     * @return Menus
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
}
