<?php
namespace App\Form;

use App\Entity\User;
use src\Validator\PasswordMatch;  // Import the custom validator
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3, 'max' => 50]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 6]),
                ],
            ])
            ->add('confirm_password', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 6]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 50]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 50]),
                ],
            ])
            ->add('number', TelType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 10, 'max' => 15]), // Adjust based on expected length
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'Please enter a valid phone number.',
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Investor' => 'ROLE_INVESTOR',
                    'ProjectLeader' => 'ROLE_PROJECTLEADER' 
                    // Add other roles here as needed
                ],
                'expanded' => true,  // This will render as a dropdown (radio buttons can be used if preferred)
                'multiple' => true,  // Single role selection
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('profileImage', FileType::class, [
                'label' => 'Profile Image',
                'mapped' => false, // This field is not mapped to the entity directly
                'required' => false,
                'constraints' => [
                    new Assert\Image([
                        'maxSize' => '5M', // Maximum file size 5MB
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Please upload a valid image (jpeg, png, gif).',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
                new PasswordMatch(), // Apply the custom validation at the form level
            ],
        ]);
    }
}

