<?php
// src/Controller/FavoriteController.php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\Post;
use App\Repository\FavoriteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/favorite')]
class FavoriteController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/toggle/{userId}/{postId}', name: 'favorite_toggle', methods: ['POST'])]
    public function toggleFavorite(int $userId, int $postId, UserRepository $userRepository, FavoriteRepository $favoriteRepository, EntityManagerInterface $em): JsonResponse
    {
        // Fetch the user by ID
        $user = $userRepository->find($userId);
        $this->logger->info('User attempted to toggle favorite', ['user_id' => $userId, 'post_id' => $postId]);

        if (!$user) {
            $this->logger->error('User not found', ['user_id' => $userId]);
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Fetch the post by ID
        $post = $em->getRepository(Post::class)->find($postId);
        if (!$post) {
            $this->logger->error('Post not found', ['post_id' => $postId]);
            return new JsonResponse(['error' => 'Post not found'], 404);
        }

        // Check if the favorite already exists
        $existingFavorite = $favoriteRepository->findOneBy(['user' => $user, 'post' => $post]);
        if ($existingFavorite) {
            $this->logger->info('Removing favorite', ['user_email' => $user->getEmail(), 'post_id' => $post->getId()]);
            $em->remove($existingFavorite);
            $em->flush();
            return new JsonResponse(['status' => 'removed']);
        }

        // Add new favorite
        $favorite = new Favorite();
        $favorite->setUser($user);
        $favorite->setPost($post);
        $this->logger->info('Adding favorite', ['user_email' => $user->getEmail(), 'post_id' => $post->getId()]);

        $em->persist($favorite);
        $em->flush();

        return new JsonResponse(['status' => 'added']);
    }
}
