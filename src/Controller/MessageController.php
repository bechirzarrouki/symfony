<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Chat;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/message')]
class MessageController extends AbstractController
{
    #[Route('/chat/{chatId}', name: 'message_list', methods: ['GET'])]
    public function list(int $chatId, EntityManagerInterface $entityManager): JsonResponse
    {
        $messages = $entityManager->getRepository(Message::class)
            ->findBy(['chat' => $chatId], ['sentAt' => 'ASC']);

        $data = array_map(function (Message $message) {
            return [
                'id' => $message->getId(),
                'sender' => $message->getSender()->getId(),
                'content' => $message->getContent(),
                'sentAt' => $message->getSentAt()->format('Y-m-d H:i:s')
            ];
        }, $messages);

        return $this->json($data);
    }

    #[Route('/create', name: 'message_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['chat'], $data['sender'], $data['content'])) {
            return $this->json(['error' => 'Invalid data'], 400);
        }

        $chat = $entityManager->getRepository(Chat::class)->find($data['chat']);
        $sender = $entityManager->getRepository(User::class)->find($data['sender']);

        if (!$chat || !$sender) {
            return $this->json(['error' => 'Chat or User not found'], 404);
        }

        $message = new Message();
        $message->setChat($chat);
        $message->setSender($sender);
        $message->setContent($data['content']);

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->json([
            'id' => $message->getId(),
            'sender' => $message->getSender()->getId(),
            'content' => $message->getContent(),
            'sentAt' => $message->getSentAt()->format('Y-m-d H:i:s')
        ], 201);
    }
}
