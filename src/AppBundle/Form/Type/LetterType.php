<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class LetterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('letter', TextType::class, array(
                    'label'  => 'Lettre',
                    'attr' => array('class' => 'form-group mb-2'),
                ))
                ->add('startTime', TimeType::class, array(                    
                    'widget' => 'single_text',
                    'label'  => 'Heure de début',
                    'attr' => array('class' => 'form-group mb-2'),
                     ))
                
                ->add('endTime', TimeType::class, array(
                    'placeholder' => 'Choose a delivery option',                   
                    'widget' => 'single_text',
                    'label'  => 'Heure de fin',
                    'attr' => array('class' => 'form-group mb-2'),
                     ))
                
                 ->add('effectiveDuration', TextType::class, array(                    
                    'label' => 'Durée effective',
                    'attr' => array('class' => 'form-group mb-2')  
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
            'data_class' => 'AppBundle\Entity\Letter'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_letter';
    }


}
