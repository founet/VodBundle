<?php

namespace Dominos\VodBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Code
 *
 * @ORM\Table(name="vod_code")
 * @ORM\Entity(repositoryClass="Dominos\VodBundle\Repository\CodeRepository")
 */
class Code
{
    // clé c'encryptage du code
    const PASSWORD = "ymog0Ni6ct5w2jI9zF4RNwANqxWbDda8137bpTsS";
    // vecteur d'initialisation
    const IV = "f9de5c9e446b06bc";
    /**
     * @var integer
     *
     * @ORM\Column(name="idcode", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

     /**
     * @ORM\ManyToOne(targetEntity="Prestataire", inversedBy="codes")
     * @ORM\JoinColumn(name="idpresta", referencedColumnName="idpresta")
     **/
    private $prestataire;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateused", type="datetime", nullable= true)
     */
    private $dateused;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetemp", type="datetime", nullable= true)
     */
    private $datetemp;


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
     * Set code
     *
     * @param string $code
     * @return Code
     */
    public function setCode($code)
    {
        $code = openssl_encrypt($code, 'aes-256-cbc',self::PASSWORD,0,self::IV);
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return openssl_decrypt($this->code, 'aes-256-cbc',self::PASSWORD,0,self::IV);
        
    }

    /**
     * Set dateused
     *
     * @param \DateTime $dateused
     * @return Code
     */
    public function setDateused($dateused)
    {
        $this->dateused = $dateused;

        return $this;
    }

    /**
     * Get dateused
     *
     * @return \DateTime 
     */
    public function getDateused()
    {
        return $this->dateused;
    }


    /**
     * Set datetemp
     *
     * @param \DateTime $datetemp
     * @return Code
     */
    public function setDatetemp($datetemp)
    {
        $this->datetemp = $datetemp;

        return $this;
    }

    /**
     * Get datetemp
     *
     * @return \DateTime 
     */
    public function getDatetemp()
    {
        return $this->datetemp;
    }

    /**
     * Set prestataire
     *
     * @param \Dominos\VodBundle\Entity\Prestaire $prestataire
     * @return Code
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
