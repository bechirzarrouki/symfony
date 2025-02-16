<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 
use Symfony\Component\HttpFoundation\File\Exception\FileException;



class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegistrationFormType::class, $user);

        $form->handleRequest($request);
        dump($form->getData());  // This will output the submitted data

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash password
            $hashedPassword = $passwordHasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($hashedPassword);

            // Set role (default to ROLE_USER)
            $user->setRoles(['ROLE_USER']);

            // Handle Profile Image Upload
            $profileImageFile = $form->get('profileImage')->getData();
            if ($profileImageFile) {
                $newFilename = uniqid() . '.' . $profileImageFile->guessExtension();
                try {
                    $profileImageFile->move(
                        $this->getParameter('profile_image_directory'),
                        $newFilename
                    );
                    $user->setProfileImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload profile image.');
                }
            }

            // Save User
            $em->persist($user);
            $em->flush();

            // Redirect to login page after successful registration
            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
