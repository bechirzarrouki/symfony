<?php
// src/Controller/PostController.php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/search/{id}', name: 'search', methods: ['GET'])]
    public function search(int $id, PostRepository $investmentRepository,UserRepository $userRepository): Response
    {
        $posts = $investmentRepository->findByUserId($id);
        $users = $userRepository->findAll();
        if (!$posts) {
            throw $this->createNotFoundException('Post not found');
        }

        return $this->render('menu/recherche.html.twig', [
            'posts' => $posts,
            'users' => $users,
        ]);
    }
    #[Route('/menu', name: 'post_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $posts = $em->getRepository(Post::class)->findAll();
        return $this->render('menu/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    // Create a new post (if you are not using a dedicated form type, you can handle request data manually)
    #[Route('/new', name: 'post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            $imageFile = $request->files->get('image');
    
            if (!$content && !$imageFile) {
                $this->addFlash('error', 'Post must have text or an image.');
                return $this->redirectToRoute('post_index');
            }
    
            $post = new Post();
            $post->setContent($content);
            
            // Retrieve user from session
            $id = $session->get('user_id');
            $user = $em->getRepository(User::class)->find($id);
    
            if (!$user) {
                // Handle missing user case
                $this->addFlash('error', 'User not found.');
                return $this->redirectToRoute('post_index');
            }
    
            $post->setAuthor($user);
    
            // Handle image upload
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $uploadDirectory = __DIR__ . '/../../public/uploads/posts';
                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0777, true);
                }
                try {
                    // Move the file to the directory where images are stored
                    $imageFile->move(
                        $uploadDirectory, // Ensure this is defined in services.yaml
                        $newFilename
                    );
            
                    // Set the image path in the post entity
                    $post->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'There was an error uploading the post image.');
                }
            }
            
    
            $em->persist($post);
            $em->flush();
    
            $this->addFlash('success', 'Post created successfully!');
            return $this->redirectToRoute('post_index');
        }
    
        return $this->render('post/new.html.twig');
    }
    
    

    // Edit an existing post
    #[Route('/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, int $id, PostRepository $postRepository,EntityManagerInterface $em): Response
{
    // Retrieve the Post entity by its ID
    $post = $postRepository->find($id);
    if (!$post) {
        throw $this->createNotFoundException('Post not found');
    }

    // Handle form submission
    if ($request->isMethod('POST')) {
        $content = $request->request->get('post_content');
        if (!$content) {
            $this->addFlash('error', 'Post content cannot be empty.');
            return $this->redirectToRoute('post_index');
        }
        
        // Update and save the post
        $post->setContent($content);
        $em->persist($post);
        $em->flush();

        $this->addFlash('success', 'Post updated successfully!');
        return $this->redirectToRoute('post_index');
    }

    // Render the edit form
    return $this->render('post/edit.html.twig', [
        'post' => $post,
    ]);
}

#[Route('/{id}', name: 'post_delete', methods: ['POST'])]
public function delete(Request $request, int $id, PostRepository $postRepository,EntityManagerInterface $em): Response
{
    // Retrieve the Post entity by its ID
    $post = $postRepository->find($id);
    if (!$post) {
        throw $this->createNotFoundException('Post not found');
    }

    // Validate CSRF token and remove the post
    if ($post) {
        $em->remove($post);
        $em->flush();
        $this->addFlash('success', 'Post deleted successfully!');
    }
    
    return $this->redirectToRoute('post_index');
}
#[Route('/post/{id}/like', name: 'post_like', methods: ['POST'])]
public function like(int $id, EntityManagerInterface $entityManager, PostRepository $investmentRepository, SessionInterface $session): Response
{
    $user_id=$session->get('user_id');
    $user =  $entityManager->getRepository(User::class)->find($user_id);
    if (!$user) {
        return $this->json(['error' => 'Unauthorized'], 403);
    }
    $investment = $investmentRepository->find($id);
    if ($investment->isLikedByUser($user)) {
        $investment->removeLike($user);
        $liked = false;
    } else {
        $investment->addLike($user);
        $liked = true;
    }

    $entityManager->persist($investment);
    $entityManager->flush();

    return $this->redirectToRoute('post_index');
}
}
