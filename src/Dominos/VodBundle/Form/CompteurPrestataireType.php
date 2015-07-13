<?php

namespace Dominos\VodBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Dominos\VodBundle\Repository\PrestataireRepository;



class CompteurPrestataireType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

            $builder->add('datepresta');
            $builder->add('nbrecodeday')
                    ->add('nbrecodeused');

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