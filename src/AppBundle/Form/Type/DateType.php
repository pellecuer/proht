<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\Type\DateType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;


class DateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->setMethod('POST')
                ->add('startDate', DateType::class, array(
            'placeholder' => 'Choose a delivery option',
            'constraints' => array(
                    new NotBlank()
            )
        ))
                ->add('endDate', DateType::class, array(
            'placeholder' => 'Choose a delivery option',
            'constraints' => array(
                    new NotBlank()
            )
        ));
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Agenda::class,
        ));
    }
}
