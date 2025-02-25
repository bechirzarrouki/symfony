<?php 
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    // Create a new comment for a given post.
    #[Route('/new/{postId}', name: 'comment_new', methods: ['POST'])]
    public function new(Request $request, int $postId, EntityManagerInterface $em,SessionInterface $session): Response
    {
        // Fetch the Post entity
        $post = $em->getRepository(Post::class)->find($postId);
        if (!$post) {
            throw $this->createNotFoundException('Post not found.');
        }

        // Retrieve the comment content
        $content = $request->request->get('comment_content');
        if (!$content) {
            $this->addFlash('error', 'Comment content cannot be empty.');
            return $this->redirectToRoute('post_index');
        }

        // Create a new Comment entity
        $comment = new Comment();
        $comment->setContent($content);
        $comment->setPost($post);
        $comment->setDateCreation(new \DateTime());
        $comment->setDateModification(new \DateTime());

        // Use a dummy user instead of a logged-in user
        $id = $session->get('user_id');
            $user = $em->getRepository(User::class)->find($id);
    
            if (!$user) {
                // Handle missing user case
                $this->addFlash('error', 'User not found.');
                return $this->redirectToRoute('post_index');
            }
        $comment->setAuthor($user);

        // Persist the comment
        $em->persist($comment);
        $em->flush();

        $this->addFlash('success', 'Comment added successfully!');
        return $this->redirectToRoute('post_index');
    }

    // Edit an existing comment
    #[Route('/{id}/edit', name: 'comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, EntityManagerInterface $em): Response
    {
        // Fetch the comment entity by ID
        $comment = $em->getRepository(Comment::class)->find($id);
        if (!$comment) {
            throw $this->createNotFoundException('Comment not found.');
        }

        // Process the POST request to update the comment
        if ($request->isMethod('POST')) {
            $content = $request->request->get('comment_content');
            if (!$content) {
                $this->addFlash('error', 'Comment content cannot be empty.');
                return $this->redirectToRoute('post_index');
            }

            // Update the comment's content and modification date
            $comment->setContent($content);
            $comment->setDateModification(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'Comment updated successfully!');
            return $this->redirectToRoute('post_index');
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
        ]);
    }
    #[Route('/{id}', name: 'comment_delete', methods: ['POST'])]
    // Delete a comment
    public function deleteform(ManagerRegistry $manager, Request $req, $id): Response
    {
        // Get the entity manager using ManagerRegistry
        $em = $manager->getManager();

        // Find the comment entity by its ID
        $comment = $em->getRepository(Comment::class)->find($id);

        // If the comment exists, remove it
        if ($comment) {
            $em->remove($comment);
            $em->flush();

            // Add a success flash message (optional)
            $this->addFlash('success', 'Comment deleted successfully.');
        } else {
            // Add an error flash message if the comment wasn't found
            $this->addFlash('error', 'Comment not found.');
        }

        // Redirect or render the response
        return $this->redirectToRoute('post_index'); // Redirect to post list or any other page
    }
}
