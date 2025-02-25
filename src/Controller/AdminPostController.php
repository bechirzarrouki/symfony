<?php
// src/Controller/AdminPostController.php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/post')]
class AdminPostController extends AbstractController
{
    #[Route('/menu', name: 'admin_post_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $posts = $em->getRepository(Post::class)->findAll();
        return $this->render('menu/indexadmin.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'admin_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            $imageFile = $request->files->get('image');
    
            if (!$content && !$imageFile) {
                $this->addFlash('error', 'Post must have text or an image.');
                return $this->redirectToRoute('admin_post_index');
            }
    
            $post = new Post();
            $post->setContent($content);
            $post->setLikes(0);
            
            $id = $session->get('user_id');
            $user = $em->getRepository(User::class)->find($id);
    
            if (!$user) {
                $this->addFlash('error', 'User not found.');
                return $this->redirectToRoute('admin_post_index');
            }
    
            $post->setAuthor($user);
    
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $uploadDirectory = __DIR__ . '/../../public/uploads/posts';
                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0777, true);
                }
                try {
                    $imageFile->move($uploadDirectory, $newFilename);
                    $post->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'There was an error uploading the post image.');
                }
            }
            
            $em->persist($post);
            $em->flush();
    
            $this->addFlash('success', 'Post created successfully!');
            return $this->redirectToRoute('admin_post_index');
        }
    
        return $this->render('post/new.html.twig');
    }

    #[Route('/{id}/edit', name: 'admin_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, PostRepository $postRepository, EntityManagerInterface $em): Response
    {
        $post = $postRepository->find($id);
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        if ($request->isMethod('POST')) {
            $content = $request->request->get('post_content');
            if (!$content) {
                $this->addFlash('error', 'Post content cannot be empty.');
                return $this->redirectToRoute('admin_post_index');
            }
            
            $post->setContent($content);
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post updated successfully!');
            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}', name: 'admin_post_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, PostRepository $postRepository, EntityManagerInterface $em): Response
    {
        $post = $postRepository->find($id);
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $em->remove($post);
        $em->flush();
        $this->addFlash('success', 'Post deleted successfully!');
        
        return $this->redirectToRoute('admin_post_index');
    }
}
