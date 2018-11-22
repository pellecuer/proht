<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\Team;
use AppBundle\Entity\Role;

class AgentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
               
            ->add('name', TextType::class, array(            
            'label'  => 'name',
            'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
            ))
               
             ->add('firstname', TextType::class, array(            
            'label'  => 'firstname',
            'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
            ))
               
            ->add('function', TextType::class, array(            
            'label'  => 'function',
            'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
            ))
               
            ->add('nni', TextType::class, array(            
            'label'  => 'code',
            'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
            ))
            
            ->add('Team', EntityType::class, array(
                'class' => Team::class,
                'choice_label' => 'name ',
                'attr' => array('class' => 'form-group mx-sm-3 mb-2')
                ))
               
            ->add('Role', EntityType::class, array(
                'class' => Role::class,
                'choice_label' => 'name ',
                'attr' => array('class' => 'form-group mx-sm-3 mb-2')   
                ))            
                        
            ->add('Envoyer', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-primary mb-2 sendDate'),
        ));
    }
}