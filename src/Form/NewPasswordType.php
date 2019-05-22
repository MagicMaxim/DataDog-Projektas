<?php

namespace App\Form;

use App\Entity\ChangePassword;
use App\Entity\User;
use function Sodium\add;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class NewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'required' => true,
                'constraints' =>
                    [
                        new NotBlank(['message' => 'Your current password']),
                        new Length(
                            [
                                'min' => 6,
                                'minMessage' => 'Password must be atleast {{ limit }} of length',
                                // max length allowed by Symfony for security reasons
                                'max' => 4096,
                            ]),
                    ],
                'invalid_message' => 'Passwords must match',
                'first_options'  => array('label' => 'New password'),
                'second_options' => array('label' => 'Repeat new password'),
            ))
                ->add('submit', SubmitType::class, array(
                'label'=>'Change password'
            ));
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}