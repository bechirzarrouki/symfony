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

#[Route('/admin/comment')]
class AdminCommentController extends AbstractController
{
    #[Route('/new/{postId}', name: 'admin_comment_new', methods: ['POST'])]
    public function new(Request $request, int $postId, EntityManagerInterface $em, SessionInterface $session): Response
    {
        $post = $em->getRepository(Post::class)->find($postId);
        if (!$post) {
            throw $this->createNotFoundException('Post not found.');
        }

        $content = $request->request->get('comment_content');
        if (!$content) {
            $this->addFlash('error', 'Comment content cannot be empty.');
            return $this->redirectToRoute('post_index_admin');
        }

        $comment = new Comment();
        $comment->setContent($content);
        $comment->setPost($post);
        $comment->setDateCreation(new \DateTime());
        $comment->setDateModification(new \DateTime());

        $id = $session->get('user_id');
        $user = $em->getRepository(User::class)->find($id);
    
        if (!$user) {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('post_index_admin');
        }
        $comment->setAuthor($user);

        $em->persist($comment);
        $em->flush();

        $this->addFlash('success', 'Comment added successfully!');
        return $this->redirectToRoute('post_index_admin');
    }

    #[Route('/{id}/edit', name: 'admin_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, EntityManagerInterface $em): Response
    {
        $comment = $em->getRepository(Comment::class)->find($id);
        if (!$comment) {
            throw $this->createNotFoundException('Comment not found.');
        }

        if ($request->isMethod('POST')) {
            $content = $request->request->get('comment_content');
            if (!$content) {
                $this->addFlash('error', 'Comment content cannot be empty.');
                return $this->redirectToRoute('post_index_admin');
            }

            $comment->setContent($content);
            $comment->setDateModification(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'Comment updated successfully!');
            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}', name: 'admin_comment_delete', methods: ['POST'])]
    public function deleteform(ManagerRegistry $manager, Request $req, $id): Response
    {
        $em = $manager->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);

        if ($comment) {
            $em->remove($comment);
            $em->flush();
            $this->addFlash('success', 'Comment deleted successfully.');
        } else {
            $this->addFlash('error', 'Comment not found.');
        }

        return $this->redirectToRoute('admin_post_index');
    }
}
