<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\User;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $roles = $request->request->all('roles') ?: ['ROLE_USER'];
            $username = $request->request->get('username');
            $phone = $request->request->get('phone'); // Get the phone number from the request
            // Validate required fields
            if (empty($email) || empty($password)) {
                $this->addFlash('error', 'Email and password are required.');
                return $this->redirectToRoute('app_register');
            }

            // Validate phone number
            if (!preg_match('/^\d{8}$/', $phone)) { // Check if phone number contains exactly 8 digits
                $this->addFlash('error', 'Phone number must be exactly 8 digits long and contain only numbers.');
                return $this->redirectToRoute('app_register');
            }

            // Create and set user data
            $user = new User();
            $user->setEmail($email);
            $user->setRoles($roles);
            $user->setUsername($username);
            $user->setNumber($phone); // Set the phone number

            // Hash the password
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            // Check for profile image
            $profileImage = $request->files->get('profile_picture');
            if ($profileImage) {
                $originalFilename = pathinfo($profileImage->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = uniqid() . '.' . $profileImage->guessExtension();

                try {
                    // Move the file to the directory where images are stored
                    $profileImage->move(
                        $this->getParameter('profile_image_directory'), // Ensure this is defined in services.yaml
                        $newFilename
                    );
                    // Set the profile image filename
                    $user->setProfileImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'There was an error uploading the profile image.');
                }
            }

            // Save user in DB
            try {
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Registration successful! You can now log in.');
                return $this->redirectToRoute('app_login');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
            }
        }

        return $this->render('login/register.html.twig');
    }
}
