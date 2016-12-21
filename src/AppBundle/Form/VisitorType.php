<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class VisitorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom du visiteur',
            ])
            ->add('nomf', TextType::class, [
                'label' => 'Nom du visiteur'
            ])
            ->add('pays', CountryType::class, [
                'label'  => 'Pays',
                'placeholder' => 'Choisissez un pays',
            ])
            ->add('dateNaissance', BirthdayType::class, [
                'label' => 'Date de naissance',
                'format' => 'dd-MM-yyyy'
                ])
            ->add('tarifReduit', CheckboxType::class, [
                'label' => 'Tarif réduit (sur présentation d\'un justificatif)',
                'required' => false
            ])
        ;
    }
}