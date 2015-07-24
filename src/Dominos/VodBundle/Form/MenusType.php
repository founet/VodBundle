<?php

namespace Dominos\VodBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenusType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('menufile', 'file');
    
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
       /* $resolver->setDefaults(array(
            'data_class' => 'Dominos\VodBundle\Entity\Menus'
        ));*/
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dominos_vodbundle_menus';
    }
}
