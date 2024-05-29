<?php

namespace App\Form;

use App\Entity\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sites', ChoiceType::class, [
                'choices' =>[

                ]
            ])
            ->add('search', SearchType::class)
            ->add('dateStart', DateType::class, [
                'html5'=> true,
                'widget' => 'single_text'
            ])
            ->add('dateEnd', DateType::class, [
                'html5'=> true
            ])
            ->add('choiseMeOrganisator', CheckboxType::class)
            ->add('choiseMeInscribed', CheckboxType::class)
            ->add('choiseMeNotInscribed', CheckboxType::class)
            ->add('finishedEvents', CheckboxType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
}
