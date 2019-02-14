<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;

use AppBundle\Entity\Team;
use AppBundle\Entity\Role;

class AgentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder               
               
            ->add('email', EmailType::class, array(
            'attr' => array('class' => 'form-group mb-2'),
                ))
                    
            ->add('username', TextType::class, array(
                'attr' => array('class' => 'form-group mb-2'),
                )
            )
               
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array(
                    'label' => 'Mot de passe',
                    'attr' => array(
                        'class' => 'form-group mb-2',
                        ),
                    ),
                'second_options' => array(
                    'label' => 'Confirmation de mot de passe',
                    'attr' => array(
                        'class' => 'form-group mb-2',
                        ),
                    ),
                )
            )      
               
            ->add('name', TextType::class, array( 
            'attr' => array('class' => 'form-group mb-2'),
            ))
               
             ->add('firstname', TextType::class, array( 
            'attr' => array('class' => 'form-group mb-2'),
            ))
               
            ->add('function', TextType::class, array(
            'attr' => array('class' => 'form-group mb-2'),
            ))
               
            ->add('nni', TextType::class, array(
            'attr' => array('class' => 'form-group mb-2'),
            ))
            
            ->add('Team', EntityType::class, array(
                'class' => Team::class,
                'choice_label' => 'name ',
                'attr' => array('class' => 'form-group mb-2'),
                'required' => false,
                ))
               
            ->add('roles', ChoiceType::class, array(                
                'attr'  =>  array('class' => 'form-control mb-2',
                
                    ),
                'choices' => array (
                    'Administrateur' => 'ROLE_ADMIN',                    
                    'Valideur' => 'ROLE_VALIDEUR',
                    'Agent' => 'ROLE_AGENT',                   
                ),
                'multiple' => true,
                'required' => true,
                )
            )
                        
            ->add('Envoyer', SubmitType::class, array(
            'attr' => array(
                'class' => 'btn btn-primary mb-2 sendDate'),
        ));      
       
    }    
    
}
