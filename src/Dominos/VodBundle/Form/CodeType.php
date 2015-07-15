<?php

namespace Dominos\VodBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Dominos\VodBundle\Repository\PrestataireRepository;
class CodeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
     public function buildForm(FormBuilderInterface $builder, array $options)
    {
      
        $builder->add('codefile', 'file', array(
                               'constraints' => array(
                                  new File(array(
                                    'maxSize'       => '10M',
                                    //'mimeTypes'   => array("text/csv,application/octet-stream"),
                                    'maxSizeMessage'       => 'Fichier trop grand',
                                    //'mimeTypesMessage'   => 'Extension autorisée : csv',
                                ))
                               )
                           )
                );


    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        /*$resolver->setDefaults(array(
            'data_class' => 'Dominos\VodBundle\Entity\Code'
        ));*/
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dominos_vodbundle_code';
    }
}
