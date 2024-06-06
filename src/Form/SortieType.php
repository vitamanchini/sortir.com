<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Place;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Status;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\ORM\EntityRepository;
use phpDocumentor\Reflection\PseudoTypes\Numeric_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('name', TextType::class, ['label' => 'Nom'])
//            ->add('dateHourStart', DateType::class, ['widget' => 'single_text', 'label' => 'Date et heure de début'])
//            ->add('duration', IntegerType::class, ['label' => 'Durée (minutes)'])
//            ->add('dateLimitInscription', DateType::class, ['widget' => 'single_text', 'label' => 'Date limite d\'inscription'])
//            ->add('maxInscriptions', IntegerType::class, ['label' => 'Nombre maximum d\'inscriptions'])
//            ->add('info', TextareaType::class, ['label' => 'Informations supplémentaires', 'required' => false])
//            ->add('place', EntityType::class, [
//                'class' => Place::class,
//                'label' => 'Lieu',
//                'choice_label' => 'name'
//            ])
//            ->add('place', EntityType::class, [
//                'class' => Place::class,
//                'label' => 'rue',
//                'choice_label' => 'street'
//            ])
//            ->add('city', EntityType::class, [
//                'class' => City::class,
//                'label' => 'code postal',
//                'choice_label' => 'postal_code'
//            ])
//            ->add('site', EntityType::class, [
//                'class' => Site::class,
//                'choice_label' => 'name',
//                'label' => 'Site organisateur '
//            ])
//            ->add('status', EntityType::class, [
//                'class'=>Status::class,
//                'choice_label'=>'label',
//                'label' => 'Statut'
//            ])
//            ->add('organizer', null, ['label' => 'Organisateur'])
//            ->add('motif', TextareaType::class, ['label' => 'Motif'])
//            ->add('place', NumberType::class, [
//                'label' => 'Latitude',
//                'required' => true,
//                'mapped' => false,
//                'scale' => 6,
//            ])
//            ->add('place', NumberType::class, [
//                'label' => 'Longitude',
//                'required' => true,
//                'mapped' => false,
//                'scale' => 6,
//            ]);

            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('dateHourStart', DateTimeType::class, ['widget' => 'single_text', 'label' => 'Date et heure de début'])
            ->add('duration', IntegerType::class, ['label' => 'Durée (minutes)'])
            ->add('dateLimitInscription', DateTimeType::class, ['widget' => 'single_text', 'label' => 'Date limite d\'inscription'])
            ->add('maxInscriptions', IntegerType::class, ['label' => 'Nombre maximum d\'inscriptions'])
            ->add('info', TextareaType::class, ['label' => 'Informations supplémentaires', 'required' => false])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'label' => 'Lieu',
                'choice_label' => 'name',

//                'mapped' => false,
            ])
//            ->add('city', EntityType::class, [
//                'class' => City::class,
//                'choice_label' => 'name',
//                'label' => 'Ville',
//            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'name',
                'label' => 'Site organisateur',
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'label',
                'label' => 'Statut',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('s')
                        ->where('s.label IN (:labels)')
                        ->setParameter('labels', ['Créée', 'Ouverte', 'Annulée']);
                },
            ]);

//            ->add('organizer', EntityType::class, [
//                'class' => Participant::class,
//                'choice_label' => 'email',
//                'label' => 'Organisateur'
//            ])
//            if ($builder->getData()->getStatus()->getId() == 6)
//            {
//                $builder->add('motif', TextareaType::class, ['label' => 'Motif']);
//            }

        }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
