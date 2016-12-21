<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class IndexType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('day', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date de la visite',
            'format' => 'dd-MM-yyyy',
            'attr' => [
                'class' => 'datepicker',
                'readonly' => 'readonly'
            ]
            ])
            ->add('dureeVisite', ChoiceType::class, [
                'choices'  => [
                    'Journée' => 'full',
                    'Demi-journée (après 14h)' => 'half'
                ],
                'label'  => 'Type de billet'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse e-mail'
            ])
            ->add('Valider', SubmitType::class)
        ;
    }
}
