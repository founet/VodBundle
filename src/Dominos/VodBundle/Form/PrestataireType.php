<?php

namespace Dominos\VodBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PrestataireType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nompresta','text', array(
                        'constraints' => array(
                            new NotBlank(array(
                                'message' => 'Le champ Nom Prestataire ne doit être vide'
                            ))
                        )
                    ))
            ->add('type', 'choice', array(
                'choices' => array('1' => 'un seul code par commande dans l\'email', 'n' => 'plusieurs codes par commande dans l\'email')
            ))
            ->add('startpresta','datetime',array(
                                'widget'=> 'single_text',
                                'format'=>'dd-MM-yyyy'))
            ->add('endpresta','datetime',array(
                                'widget'=> 'single_text',
                                'format'=>'dd-MM-yyyy'))
        ;



    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dominos\VodBundle\Entity\Prestataire'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dominos_vodbundle_prestataire';
    }
}
