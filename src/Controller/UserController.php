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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Dompdf\Dompdf;
use Dompdf\Options;

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

    /**
     * @Route("/create", name="user_create")
     */
    public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        $user->setUsername($request->get('username'));
        $user->setEmail($request->get('email'));
        
        $user->setPassword($passwordHasher->hashPassword($user, $request->get('password')));

        $roles = $request->get('roles');
        if ($roles) {
            $user->setRoles($roles);
        }

        if (!$user->getUsername() || !$user->getEmail() || !$user->getPassword()) {
            $this->addFlash('error', 'All fields are required.');
            return $this->redirectToRoute('user_create');
        }

        $existingUser = $em->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);
        if ($existingUser) {
            $this->addFlash('error', 'Username already taken.');
            return $this->redirectToRoute('user_create');
        }

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('app_login');
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
     * @Route("/admin/userpdf", name="app_userpdf")
     */
    public function generatePdf(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        $html = '<h1>User List</h1>';
        $html .= '<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
        $html .= '<thead><tr><th>Username</th><th>Email</th><th>Phone Number</th><th>Role</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($users as $user) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($user->getUsername()) . '</td>';
            $html .= '<td>' . htmlspecialchars($user->getEmail()) . '</td>';
            $html .= '<td>' . htmlspecialchars($user->getNumber()) . '</td>';
            $html .= '<td>' . htmlspecialchars(implode(', ', $user->getRoles())) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfOutput = $dompdf->output();

        return new Response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="user_list.pdf"',
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit")
     */
    public function edit(Request $request, EntityManagerInterface $em, UserRepository $userRepository, int $id): Response
    {
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
     * @Route("/user/{id}/ban", name="user_ban")
     */
    public function banUser(int $id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('app_userlist');
        }

        $user->setBanned(!$user->isBanned());
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', $user->isBanned() ? 'User banned successfully.' : 'User unbanned successfully.');
        return $this->redirectToRoute('app_userlist');
    }

 /**
 * @Route("/user/search", name="user_search", methods={"GET"})
 */
public function search(Request $request, UserRepository $userRepository): Response
{
    // Get the email search term from the query parameters
    $emailSearch = $request->query->get('email');

    // Fetch users matching the email search term
    $users = [];
    if ($emailSearch) {
        $users = $userRepository->findByEmailLike($emailSearch);
    }

    // Render the user list template with the search results
    return $this->render('back/userlist.html.twig', [
        'users' => $users,
        'searchQuery' => $emailSearch, // Pass the search query back to the template
    ]);
}

    /**
     * @Route("/user/{id}/delete", name="user_delete")
     */
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('app_userlist');
        }

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
                $session->set('user', $user);
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

#[Route('/profile', name: 'app_profile')]
public function profile(SessionInterface $session): Response
{
    $user = $session->get('user');

    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    return $this->render('login/edit.html.twig', [
        'user' => $user,
    ]);
}

}