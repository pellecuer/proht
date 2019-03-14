<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RuleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('maxHourPerDay', TextType::class, array(
                    'label'  => 'Nb heures max/jour',
                    'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
                ))                
                 ->add('minRestPerWeek', TextType::class, array(
                    'label'  => 'Heures de repos min/semaine',
                    'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
                ))                
                ->add('minRestBetweenDays', TextType::class, array(
                    'label'  => 'Heures de repos min entre deux jours',
                    'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
                ))
                ->add('maxHourperWeek', TextType::class, array(
                    'label'  => 'Heures max/semaine',
                    'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
                ))
                ->add('maxAveragePerWeek', TextType::class, array(
                    'label'  => 'moyenne max/semaine',
                    'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
                ))
                 ->add('nbWeekForAverage', TextType::class, array(
                    'label'  => ' Nb semaines pour le cacul de la moyenne',
                    'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
                ))
              
                ->add('Envoyer', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-outline-dark'),
            )); 
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Rule'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_Rule';
    }


}
