<?php
// src/Controller/PostController.php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\PostRepository;
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

}
