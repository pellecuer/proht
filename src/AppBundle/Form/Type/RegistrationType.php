<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Agent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class RegistrationType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(                
                'attr' => array('class' => 'form-group mb-2'),
            ))
                
            ->add('username', TextType::class, array(                
                'attr' => array('class' => 'form-group mb-2'),
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'attr' => array('class' => 'form-group mb-2'),
                'type' => PasswordType::class,
                'first_options'  => array(
                    'label' => 'Password',
                    'attr' => array('class' => 'form-group mb-2'),
                    ),
                'second_options' => array(
                    'label' => 'Repeat Password',
                    'attr' => array('class' => 'form-group mb-2'),
                    ),
            ))
            ->add('Envoyer', SubmitType::class, array(
            'attr' => array(
                'class' => 'btn btn-primary my-2 sendDate'),
                
            ))  
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Agent::class,
        ));
    }
}
