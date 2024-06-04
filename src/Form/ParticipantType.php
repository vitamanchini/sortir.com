<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Participant;
use App\Entity\Site;
use App\Repository\ParticipantRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => function (Site $site) {
                    return sprintf('%s', $site->getName());
                },
                'placeholder' => 'Sélectionnez une ville',
            ])
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, [
                'choices' => ['admin' => 'ROLE_ADMIN', 'user' => 'ROLE_USER']
            ])
            ->add('password', PasswordType::class)
            ->add('name', TextType::class)
            ->add('secondName', TextType::class)
            ->add('telephone', TextType::class)
            ->add('active', CheckboxType::class, [
                'row_attr' => ['class' => 'form-check form-switch'],
                'attr' => ['class' => 'form-check-input', 'role' => 'switch'],
            ])
            ->add('pseudo', TextType::class)
            ->add('profileImage', FileType::class, [
                "data_class" => null
            ])
//            ->add('save', SubmitType::class, ['label' => 'Sauvegarder les modifications']);
        ;

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    return count($rolesArray)? $rolesArray[0]: null;
                },
                function ($rolesString) {
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
            'required' => false
        ]);
    }
}
