<?php

namespace Dominos\VodBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;



class CompteurPrestataireType extends AbstractType
{
    private $dates ;

    public function __construct(){
      
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $compteur = $builder->getData();
            
            $dates = $compteur->getPrestataire()->getPrestaPeriod();
            if(is_null($compteur->getId())){
                
            }
            $builder->add('datepresta','choice', array('choices' => $dates));
            $builder->add('nbrecodeday','number', array(
                                'constraints' => array(
                                    new NotBlank(array(
                                        'message' => 'Le champ Nombre de code ne doit être vide'
                                    ))
                                )
                    ));

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
