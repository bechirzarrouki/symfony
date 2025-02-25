<?php 
// src/Controller/MailController.php
namespace App\Controller;

use App\Service\GmailOAuth2TokenProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    private GmailOAuth2TokenProvider $tokenProvider;

    public function __construct(GmailOAuth2TokenProvider $tokenProvider)
    {
        $this->tokenProvider = $tokenProvider;
    }

    #[Route('/send', name: 'mail_index')]
    public function sendMail(): Response
    {
        // Get the latest access token
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
            ->to('bechir.zarrouki00@gmail.com')
            ->subject('Test Email via Gmail OAuth2')
            ->text('This email is sent using Gmail OAuth2 authentication with Symfony Mailer.');

        // Send the email
        $mailer->send($email);

        return new Response('Email sent successfully.');
    }
}
