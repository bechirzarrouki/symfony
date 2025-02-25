<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\ChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/chat')]
class ChatController extends AbstractController
{
    #[Route('/message', name: 'app_conversations')]
    public function index(ChatRepository $conversationRepository,SessionInterface $session): Response
    {
        $currentUser = $session->get('user'); // Récupère l'utilisateur connecté
    
        if (!$currentUser) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }
    
        $conversations = $conversationRepository->findByUser($currentUser);
    
        return $this->render('message/index.html.twig', [
            'conversations' => $conversations,
        ]);
    }
    

    #[Route('/create/{id1}/{id2}', name: 'chat_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, int $id1, int $id2): Response
    {
        // Fetch users based on IDs
        $user1 = $entityManager->getRepository(User::class)->find($id1);
        $user2 = $entityManager->getRepository(User::class)->find($id2);
    
        if (!$user1 || !$user2) {
            return $this->json(['error' => 'Users not found'], 404);
        }
    
        // Create the chat
        $chat = new Chat();
        $chat->setUser1($user1);
        $chat->setUser2($user2);
    
        // Persist the chat
        $entityManager->persist($chat);
        $entityManager->flush();
    
        // Create a message in the chat
        $messageContent = $request->request->get('message');// Default message or content from request
        $message = new Message();
        $message->setChat($chat);
        $message->setSender($user2);  // Set user2 as the sender
        $message->setContent($messageContent);
    
        // Persist the message
        $entityManager->persist($message);
        $entityManager->flush();
    
        // Return the response with chat and message data
        return $this->redirectToRoute('app_conversations');
    }
    
    #[Route('/{id}', name: 'chat_show', methods: ['GET'])]
    public function show(Chat $chat): JsonResponse
    {
        return $this->json($chat);
    }

    #[Route('/{id}', name: 'chat_delete', methods: ['DELETE'])]
    public function delete(Chat $chat, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($chat);
        $entityManager->flush();

        return $this->json(['message' => 'Chat deleted']);
    }
}
