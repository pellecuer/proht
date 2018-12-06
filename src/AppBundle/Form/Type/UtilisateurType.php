<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UtilisateurType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', TextType::class, array(
                    'label'  => 'Nom',
                    'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
                ))
                
                ->add('nni', TextType::class, array(
                    'label'  => 'nni',
                    'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
                ))
                
                ->add('Role', EntityType::class, array(
                'class' => Role::class,
                'choice_label' => 'name ',
                'attr' => array('class' => 'form-group mx-sm-3 mb-2')
                ))
              
                ->add('Envoyer', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-primary mb-2 sendDate'),
            )); 
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Utilisateur'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_Utilisateur';
    }
}
