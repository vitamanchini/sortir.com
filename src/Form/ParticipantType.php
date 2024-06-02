<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Participant;
use App\Entity\Site;
use App\Repository\ParticipantRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('name', TextType::class)
            ->add('secondName', TextType::class)
            ->add('pseudo', TextType::class)
            ->add('telephone', TextType::class)
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => function (Site $site) {
                    return sprintf('%s', $site->getName());
                },
                'placeholder' => 'Sélectionnez une ville',
            ])
            ->add('save', SubmitType::class, ['label' => 'Sauvegarder les modifications']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
