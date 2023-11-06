<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom',
                'required'   => true,
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'input-required',
                ]
            ])
            ->add('prenom', null, [
                'label' => 'Prénom',
                'required'   => true,
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'input-required',
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'email',
                'required'   => true,
                'attr' => [
                    'placeholder' => 'email',
                    'class' => 'input-required',
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Accepter les termes',
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'invalid_message' => 'Les mots de passe ne correspondent pas!',
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Mot de passe', 
                        'class' => 'form-control'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmer le mot de passe', 
                        'class' => 'mt-1 form-control'
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Saisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit faire au minimu {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
