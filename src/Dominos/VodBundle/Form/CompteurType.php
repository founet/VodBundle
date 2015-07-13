<?php

namespace Dominos\VodBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Dominos\VodBundle\Repository\PrestataireRepository;
use Dominos\VodBundle\Entity\Compteur;
use Dominos\VodBundle\Entity\Prestataire;


class CompteurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prestataire', 'entity', array(
                'class'    => 'DominosVodBundle:Prestataire',
                'choice_label' => 'nompresta',
                'required' => false,
                'empty_value' => 'Selectionner un prestataire',
                'query_builder' => function(PrestataireRepository $r) {
                    return $r->getQueryPrestataires();
                },
                'multiple' => false,
                'label'=>'Prestataires')
            );

        $formModifier = function(FormInterface $form, Prestataire $prestataire = null) {

            $dates =  null === $prestataire ? array() : $prestataire->getPrestaPeriod();
            $form->add('datepresta', 'choice', array('choices' => $dates));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($formModifier) {
               
                $compteur = $event->getData();
                $prestataire = $compteur->getPrestataire();

                $formModifier($event->getForm(), $prestataire);
            }
        );

        $builder->get('prestataire')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($formModifier) {
                // Il est important de récupérer ici $event->getForm()->getData(),
                // car $event->getData() vous renverra la données initiale (vide)
                $prestataire = $event->getForm()->getData();

                // puisque nous avons ajouté l'écouteur à l'enfant, il faudra passer
                // le parent aux fonctions de callback!
                $formModifier($event->getForm()->getParent(), $prestataire);
            }
        );
        $builder->add('nbrecodeday')
               ->add('nbrecodeused')// init to 0
        ;

    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dominos\VodBundle\Entity\Compteur'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dominos_vodbundle_compteur';
    }
}
