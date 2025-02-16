<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\UserRegistrationFormType; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\PasswordHasherInterface; // Import the PasswordHasherInterface

class UserController extends AbstractController
{
    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function dashboard(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('back/dashboard.html.twig', [
            'users' => $users,
        ]);
    }

    // Register or create a new user
    /**
     * @Route("/create", name="user_create")
     */
    public function create(Request $request, EntityManagerInterface $em, PasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        // Set user properties from the form
        $user->setUsername($request->get('username'));
        $user->setEmail($request->get('email'));
        
        // Hash the password using PasswordHasherInterface
        $user->setPassword($passwordHasher->hash($request->get('password')));
        
        $user->setFirstname($request->get('first_name'));
        $user->setLastname($request->get('last_name'));

        // Handle role
        $roles = $request->get('roles');
        if ($roles) {
            $user->setRoles($roles); // The 'roles' array will hold the selected roles
        }

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('app_login'); // Redirect after registration
    }

    /**
     * @Route("/user/{id}", name="user_show")
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit")
     */
    public function edit(User $user, Request $request, EntityManagerInterface $em, PasswordHasherInterface $passwordHasher): Response
    {
        // Create the form for editing user data
        $form = $this->createForm(UserRegistrationFormType::class, $user);

        // Handle the form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If the password is updated, hash the new password
            if ($request->get('password')) {
                $hashedPassword = $passwordHasher->hash($request->get('password'));
                $user->setPassword($hashedPassword);
            }

            // Handle profile image upload if updated
            $profileImageFile = $form->get('profileImage')->getData();
            if ($profileImageFile) {
                $newFilename = uniqid() . '.' . $profileImageFile->guessExtension();
                try {
                    $profileImageFile->move(
                        $this->getParameter('profile_image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle error if image upload fails
                }
                $user->setProfileImage($newFilename);
            }

            // Handle role (if changed)
            $role = $form->get('role')->getData();
            $user->setRoles([$role]);

            // Save the updated user data to the database
            $em->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('back/edituser.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/{id}/delete", name="user_delete")
     */
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('user_index');
    }
}

