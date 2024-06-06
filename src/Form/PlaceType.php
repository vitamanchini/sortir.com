<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Place;
use SebastianBergmann\CodeCoverage\Report\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cityName', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'cityName',
                'label' => 'Ville',
            ])
            ->add('postalCode', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'postalCode',
                'label' => 'code postal',
            ])
            ->add('street', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'street',
                'label' => 'rue',
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude',
                'required' => false

            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}