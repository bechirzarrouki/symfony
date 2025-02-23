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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Import the PasswordHasherInterface
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/userlist", name="app_dashboard")
     */
    public function userList(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('back/userlist.html.twig', [
            'users' => $users,
        ]);
    }

    // Register or create a new user
    /**
     * @Route("/create", name="user_create")
     */
    public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        // Set user properties from the form
        $user->setUsername($request->get('username'));
        $user->setEmail($request->get('email'));
        
        // Hash the password using PasswordHasherInterface
        $user->setPassword($passwordHasher->HashPassword($user,$request->get('password')));
        
    

        // Handle role
        $roles = $request->get('roles');
        if ($roles) {
            $user->setRoles($roles); // The 'roles' array will hold the selected roles
        }

        // Validate user input (optional but recommended)
        if (!$user->getUsername() || !$user->getEmail() || !$user->getPassword()) {
            $this->addFlash('error', 'All fields are required.');
            return $this->redirectToRoute('app_login');
        }

        // Check for existing user
        $existingUser = $em->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);
        if ($existingUser) {
            $this->addFlash('error', 'Username already taken.');
            return $this->redirectToRoute('app_login');
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
        return $this->render('back/userlist.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit")
     */
    public function edit(Request $request, EntityManagerInterface $em, UserRepository $userRepository, int $id): Response {
        $user = $userRepository->find($id);
        if (!$user) {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('app_userlist');
        }
        $user->setUsername($request->request->get('username'));
        $user->setEmail($request->request->get('email'));
        $user->setNumber($request->request->get('number'));
    
        $em->persist($user);
        $em->flush();
    
        $this->addFlash('success', 'User updated successfully.');
        return $this->redirectToRoute('app_userlist');
    }

    /**
     * @Route("/user/{id}/delete", name="user_delete")
     */
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        // Fetch user from the database
        $user = $em->getRepository(User::class)->find($id);

        // Check if user exists
        if (!$user) {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('app_userlist');
        }

        // Remove the user
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'User deleted successfully.');

        return $this->redirectToRoute('app_userlist');
    }
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, EntityManagerInterface $em,UserPasswordHasherInterface  $passwordHasher, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);

            if (!$user) {
                $this->addFlash('error', 'User not found.');
                return $this->redirectToRoute('app_login');
            }

            // Using PasswordHasherInterface to validate the password
            if ($passwordHasher->isPasswordValid($user, $password)) {
                // Store user session
                $session->set('user_id', $user->getId());
                $session->set('user_email', $user->getEmail());
                $session->set('username', $user->getUsername());
                $session->set('Roles',$user->getRoles());
                $this->addFlash('success', 'Login successful!');
                if (in_array("ROLE_admin", $user->getRoles()))
                    return $this->redirectToRoute('app_userlist');
                else
                    return $this->redirectToRoute('post_index');
            } else {
                $this->addFlash('error', 'Invalid password.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('login/login.html.twig');
    }


}
