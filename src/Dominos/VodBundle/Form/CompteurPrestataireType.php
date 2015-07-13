<?php

namespace Dominos\VodBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Dominos\VodBundle\Repository\PrestataireRepository;



class CompteurPrestataireType extends AbstractType
{
    private $dates ;

    public function __construct($dates){
        $this->dates = $dates;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $dates = $this->dates;
            $builder->add('datepresta','choice', array('choices' => $dates));
            $builder->add('nbrecodeday');
                    

    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        /*$resolver->setDefaults(array(
            'data_class' => 'Dominos\VodBundle\Entity\Compteur'
        ));*/
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dominos_vodbundle_compteurprestataire';
    }
}
