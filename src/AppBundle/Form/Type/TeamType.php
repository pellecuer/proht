<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Event;
use AppBundle\Entity\Agent;
use AppBundle\Entity\Section;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TeamType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', TextType::class, array(                    
                    'attr' => array(
                        'class' => 'form-group mb-2',                        
                        ),
                ))                                                  
                ->add('Event', EntityType::class, array(
                'class' => Event::class,
                'choice_label' => 'code',
                'attr' => array('class' => 'form-group mb-2')  
                ))                
                
                 ->add('Section', EntityType::class, array(
                'class' => Section::class,
                'choice_label' => 'name',
                'attr' => array('class' => 'form-group mb-2')  
                ))
                
                ->add('Envoyer', SubmitType::class, array(
            'attr' => array(
                'class' => 'btn btn-primary mb-2 sendDate'),                
            ));
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Team'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_team';
    }


}
