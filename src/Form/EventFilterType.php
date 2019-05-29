<?php

namespace App\Form;

use App\Entity\Category;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\DateTime;

class EventFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title',TextType::class,  [
            'label' => false,
            'attr' => [
                'placeholder' => 'Filtruoti pagal pavadinima',
            ]])
                    ->add('description',TextType::class,  [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'Filtruoti pagal aprašyma',
                        ]])
                ->add('price', MoneyType::class, [
                    "label" => false,
                    'attr' => [
                        'placeholder' => 'Filtruoti pagal kaina',
                    ]])
                ->add('location',TextType::class,  [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Filtruoti pagal vieta',
                    ]]);
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
