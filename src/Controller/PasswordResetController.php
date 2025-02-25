<?php
// src/Controller/PasswordResetController.php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use App\Service\GmailOAuth2TokenProvider;
use Doctrine\ORM\EntityManagerInterface;
use Google\Service\Docs\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class PasswordResetController extends AbstractController
{
    public function __construct(GmailOAuth2TokenProvider $tokenProvider)
    {
        $this->tokenProvider = $tokenProvider;
    }
    /**
     * @Route("/send-reset-email", name="send_reset_email", methods={"GET"})
     */
    public function sendResetEmail(Request $request,UserRepository $ur): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        // Validate email
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid email address.']);
        }

        // Generate a reset link

        $user=$ur->findByEmailLike($email);
        $resetLink = "http://127.0.0.1:8000/reset-password/" . $user[0]->getId();

        // Send the email
        try {

            $accessToken = $this->tokenProvider->getAccessToken();

            // Build the DSN: replace 'your-email@gmail.com' with your actual Gmail address
            $mailerDsn = sprintf(
                'smtp://%s:%s@smtp.gmail.com:587?encryption=tls&auth_mode=xoauth2',
                'yager.2250@gmail.com',
                $accessToken
            );
    
            // Create the transport and mailer
            $transport = Transport::fromDsn($mailerDsn);
            $mailer = new Mailer($transport);
    
            // Compose your email
            $email = (new Email())
                ->from('yager.2250@gmail.com')
                ->to($email)
                ->subject('this a prediction on your last investment post')
                ->text($resetLink);
    
            // Send the email
            $mailer->send($email);
    

            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Failed to send email.']);
        }
    }
    #[Route('/reset-password/{id}', name: 'app_reset_password')]
    public function resetPassword(
        int $id, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher
    ): HttpFoundationResponse {
        // Fetch user by ID
        $user = $entityManager->getRepository(User::class)->find($id);
    
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');
    
            // Validate password match
            if ($password !== $confirmPassword) {
                return $this->render('login/newpassword.html.twig', [
                    'error' => 'Passwords do not match',
                ]);
            }
    
            // Hash the new password
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
    
            // Save to the database
            $entityManager->persist($user);
            $entityManager->flush();
    
            // Redirect to login after reset
            return $this->redirectToRoute('app_login');
        }
    
        // Always pass 'error' variable to avoid Twig errors
        return $this->render('login/newpassword.html.twig', ['error' => null]);
    }
}
