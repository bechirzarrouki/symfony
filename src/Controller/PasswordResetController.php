<?php
// src/Controller/PasswordResetController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class PasswordResetController extends AbstractController
{
    /**
     * @Route("/send-reset-email", name="send_reset_email", methods={"POST"})
     */
    public function sendResetEmail(Request $request, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid email address.']);
        }

        // Generate a reset link (you can use a token or unique identifier)
        $resetLink = "http://yourwebsite.com/reset-password?token=" . bin2hex(random_bytes(16));

        // Send the email
        try {
            $emailMessage = (new Email())
                ->from('no-reply@yourwebsite.com')
                ->to($email)
                ->subject('Password Reset')
                ->text("Restore your password here: $resetLink");

            $mailer->send($emailMessage);

            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Failed to send email.']);
        }
    }
}