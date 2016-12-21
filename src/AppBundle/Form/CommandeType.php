<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CommandeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visitors', CollectionType::class, [
                'entry_type'   => VisitorType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'label'        => ' ',
                'by_reference' => false
            ])
            ->add('montant', HiddenType::class)
            ->add('Valider votre commande', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary']
            ])
        ;
    }
}
