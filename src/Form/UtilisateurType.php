<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('phone', TextType::class)
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => function (City $city) {
                    return sprintf('%s (%s)', $city->getNom(), $city->getCodePostal());
                },
                'placeholder' => 'Sélectionnez une ville',
            ])
            ->add('save', SubmitType::class, ['label' => 'Sauvegarder les modifications']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
