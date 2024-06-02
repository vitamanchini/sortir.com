<?php

namespace App\Form;

use App\Entity\SearchData;
use App\Entity\Site;
use App\Repository\SiteRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
        $sites =
            $builder
                ->add('site', EntityType::class, [
                    'class' => Site::class,
                    'choice_label' => 'name',
                    'label' => 'Site organisateur '
                ])
                ->add('search', SearchType::class, [
                    'label' => 'Le nom de la sortie contient'])
                ->add('dateStart', DateType::class, [
                    'html5' => true,
                    'widget' => 'single_text',
                    'label' => 'Entre '
                ])
                ->add('dateEnd', DateType::class, [
                    'html5' => true,
                    'widget' => 'single_text',
                    'label' => ' et '
                ])
                ->add('choiseMeOrganisator', CheckboxType::class, [
                    'label' => 'Sorties dont je suis l\'organisatrice'])
                ->add('choiseMeInscribed', CheckboxType::class, [
                    'label' => 'Sorties auquelles je suis inscrit/e'])
                ->add('choiseMeNotInscribed', CheckboxType::class, [
                    'label' => 'Sorties auquelles je ne suis pas inscrit/e'])
                ->add('finishedEvents', CheckboxType::class, [
                    'label' => 'Sorties passées']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
}
