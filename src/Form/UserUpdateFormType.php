<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserUpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class, [
                'required' => false, // Password is optional during update
            ])
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('number', TextType::class) // Assuming 'number' is a phone number or similar
            ->add('profilepic', FileType::class, [
                'label' => 'Profile Picture (Optional)',
                'required' => false, // If no file is uploaded, don't modify the profile picture
                'mapped' => false, // This prevents Symfony from binding the file to the entity
                'multiple' => false, // We expect only one file to be uploaded
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
