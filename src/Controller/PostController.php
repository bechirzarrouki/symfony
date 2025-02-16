<?php
// src/Controller/PostController.php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post')]
class PostController extends AbstractController
{
    // List all posts
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
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            if (!$content) {
                $this->addFlash('error', 'Post content cannot be empty.');
                return $this->redirectToRoute('post_index');
            }
            
            $post = new Post();
            $post->setContent($content);
            $post->setLikes(0);
    
            // Instead of using $this->getUser(), use a dummy user:
            $dummyUser = $em->getRepository(User::class)->findOneBy([]);
            if (!$dummyUser) {
                // Create a dummy user if none exists.
                $dummyUser = new User();
                $dummyUser->setName('Dummy User');
                $dummyUser->setEmail('dummy@example.com');
                $dummyUser->setRole('ROLE_CLIENT'); // or any role you prefer
                $em->persist($dummyUser);
                $em->flush();
            }
            $post->setAuthor($dummyUser);
    
            $em->persist($post);
            $em->flush();
    
            $this->addFlash('success', 'Post created successfully!');
            return $this->redirectToRoute('post_index');
        }
    
        return $this->render('post/new.html.twig');
    }
    

    // Edit an existing post
    #[Route('/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        // (Optionally check if the current user is allowed to edit this post)
        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            if (!$content) {
                $this->addFlash('error', 'Post content cannot be empty.');
                return $this->redirectToRoute('post_index');
            }
            $post->setContent($content);
            $em->flush();

            $this->addFlash('success', 'Post updated successfully!');
            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
        ]);
    }

    // Delete a post
    #[Route('/{id}', name: 'post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $em->remove($post);
            $em->flush();
            $this->addFlash('success', 'Post deleted successfully!');
        }
        return $this->redirectToRoute('post_index');
    }
}
