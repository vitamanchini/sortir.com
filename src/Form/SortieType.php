<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('dateHourStart', DateType::class, ['widget' => 'single_text', 'label' => 'Date et heure de début'])
            ->add('duration', IntegerType::class, ['label' => 'Durée (minutes)'])
            ->add('dateLimitInscription', DateType::class, ['widget' => 'single_text', 'label' => 'Date limite d\'inscription'])
            ->add('maxInscriptions', IntegerType::class, ['label' => 'Nombre maximum d\'inscriptions'])
            ->add('info', TextareaType::class, ['label' => 'Informations supplémentaires', 'required' => false])
            ->add('place', null, ['label' => 'Lieu'])
            ->add('site', null, ['label' => 'Site'])
            ->add('status', null, ['label' => 'Statut'])
            ->add('organizer', null, ['label' => 'Organisateur']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
