<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormBuilderInterface;



class EventType extends AbstractType    
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder                
               ->add('startDate', DateType::class, array(
            'placeholder' => 'Choose a delivery option',
            'constraints' => array(
                    new NotBlank()
            ),
            'widget' => 'single_text',
            'label'  => 'Date de début',
            'attr' => array('class' => 'form-group mb-2'),
             ))
                
            ->add('endDate', DateType::class, array(
            'placeholder' => 'Choose a delivery option',
            'constraints' => array(
                    new NotBlank()
            ),
            'widget' => 'single_text',
            'label'  => 'Date de fin',
            'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
            ))
                
            ->add('code', TextType::class, array(            
            'label'  => 'code',
            'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
            ))
            
            ->add('eventType', ChoiceType::class, array(
                'choices' => array(
                    'Vacances scolaires' => 'Vacances scolaires',
                    'Copat' => 'copat',
                    'Rcd' => 'RCD',
                    'Arrêt de tranche' => 'Arrêt de tranche'
                    ),
            'placeholder' => 'Choisissez une option',
            'constraints' => array(
                    new NotBlank()
            ),            
            'label'  => 'type',
            'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
            ))    
                        
            ->add('Envoyer', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-primary mb-2 sendDate'),
            ));   
    }    
}
