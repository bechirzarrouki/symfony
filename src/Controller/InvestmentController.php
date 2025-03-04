<?php

namespace App\Controller;
use App\Service\GmailOAuth2TokenProvider;
use App\Service\FlaskApiService;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use App\Entity\Investment;
use App\Entity\InvestmentFavorite;
use App\Entity\User;
use App\Repository\FavoriteRepository;
use App\Repository\InvestmentFavoriteRepository;
use App\Repository\InvestmentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

    

#[Route('/investment')]
class InvestmentController extends AbstractController
{    private LoggerInterface $logger;

    
    public function __construct(GmailOAuth2TokenProvider $tokenProvider,LoggerInterface $logger)
    {
        $this->tokenProvider = $tokenProvider;
        $this->logger = $logger;
    }
    public function sendMail(String $data,String $email): Response
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
            ->to($email)
            ->subject('this a prediction on your last investment post')
            ->text($data);

        // Send the email
        $mailer->send($email);

        return new Response('Email sent successfully.');
    }
    // List all investments
    #[Route('/', name: 'investment_index', methods: ['GET'])]
    public function index(InvestmentRepository $investmentRepository,SessionInterface $session): Response
    {
        $id=$session->get('user_id');
        $investments = $investmentRepository->findByUserId($id);
        return $this->render('investisement/index.html.twig', [
            'investments' =>  $investments,
        ]);
    }
    #[Route('/static', name: 'investment_statistic', methods: ['GET'])]
    public function statistic(InvestmentRepository $investmentRepository,SessionInterface $session): Response
    {
        $investments = $investmentRepository->findAll();
        return $this->render('static/static.html.twig', [
            'investments' =>  $investments,
        ]);
    }
    #[Route('/investisementadmin', name: 'investment_index_admin', methods: ['GET'])]
    public function indexadmin(InvestmentRepository $investmentRepository): Response
    {
        return $this->render('backoffice/investissement/investisementadmin.html.twig', [
            'investments' => $investmentRepository->findAll(),
        ]);
    }
    #[Route('/search/{id}', name: 'searchi', methods: ['GET'])]
public function search(int $id, InvestmentRepository $investmentRepository,UserRepository $userRepository): Response
{
    $investments = $investmentRepository->findByUserId($id);
    $users = $userRepository->findAll();
    if (!$investments) {
        throw $this->createNotFoundException('Investment not found');
    }

    return $this->render('investisement/recherche.html.twig', [
        'investments' => $investments,
        'users'=> $users,
    ]);
}

    #[Route('/investisementafficher', name: 'investment_index_afficher', methods: ['GET'])]
    public function indexaffichage(InvestmentRepository $investmentRepository,UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('investisement/investisement.html.twig', [
            'investments' => $investmentRepository->findAll(),
            'users'=>$users,
        ]);
    }

    // Create a new investment using a dummy user if not logged in
    #[Route('/new', name: 'investment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, InvestmentRepository $investmentRepository, EntityManagerInterface $em, SessionInterface $session,FlaskApiService $api): Response
    {
        $investment = new Investment();
        
        if ($request->isMethod('POST')) {
            // Retrieve values from the request
            $content = $request->request->get('content');
            $investmentTypes = $request->request->all('investmentTypes');
            $taxRendement = (float) $request->request->get('taxRendement');  // Assuming it's a float
            $Description = $request->request->get('description');
            $TypeReturn = $request->request->get('typeReturn');
            $dateDeadline = new \DateTime($request->request->get('dateDeadline'));
            $status = $request->request->get('status');

            // Set investment values
            $investment->setContent($content);
            $investment->setInvestmentTypes($investmentTypes);
            // Use the logged-in user if available, otherwise use/create a dummy user
            $user = new User;
            $user->setId((int)$session->get('user_id'));
            $user->setUsername($session->get('username'));
            $user->setEmail($session->get('user_email'));
            $user->setRoles($session->get('Roles'));
            $user = $em->getRepository(User::class)->find($session->get('user_id'));
            $investment->setUser($user);
    
            // Save the new investment
            $investmentRepository->save($investment);
    
            // Get the ID of the newly created investment
            $id = $investment->getId();
            
            // Store the necessary data in the session to be used in the 'return_create' controller
            $session->set('investmentData', [
                'taxRendement'=>$taxRendement,
                'Description'=>$Description,
                'TypeReturn'=>$TypeReturn,
                'dateDeadline'=>$dateDeadline,
                'status'=>$status,
            ]);
            $type=implode(',', $investmentTypes);
            $mail=$api->predictROI($content,$type,(float)$TypeReturn);
            $data = json_encode($mail, JSON_UNESCAPED_SLASHES);
            $this->sendMail($data,$user->getEmail());
            // Flash message for successful investment creation
            $this->addFlash('success', 'Investment created successfully!');
            // Redirect to the return creation page with the investment ID
            return $this->redirectToRoute('return_create', ['investmentId' => $id]);
        }
    
        return $this->render('investment/new.html.twig', [
            'investment' => $investment,
        ]);
    }
    

    // Display a single investment
    #[Route('/{id}', name: 'investment_show', methods: ['GET'])]
    public function show(Investment $investment): Response
    {
        return $this->render('investment/show.html.twig', [
            'investment' => $investment,
        ]);
    }

    // Edit an existing investment
    #[Route('/{id}/edit', name: 'investment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, InvestmentRepository $investmentRepository): Response
    {
        // Fetch the Investment entity by its ID
        $investment = $investmentRepository->find($id);

        // If the investment is not found, throw a 404 error
        if (!$investment) {
            throw $this->createNotFoundException('Investment not found');
        }

        // Handle the form submission
        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');

            // Update the investment entity
            $investment->setContent($content);

            // Save the updated entity
            $investmentRepository->save($investment);

            // Add a flash message
            $this->addFlash('success', 'Investment updated successfully!');

            // Redirect to the investment index page
            return $this->redirectToRoute('investment_index');
        }

        // Render the edit form
        return $this->render('investisement/index.html.twig', [
            'investment' => $investment,
        ]);
    }

    // Delete an investment
    #[Route('/{id}', name: 'investment_delete', methods: ['POST'])]
    public function delete(Request $request, InvestmentRepository $investmentRepository, $id): Response
    {
        // Retrieve the investment entity by ID
        $investment = $investmentRepository->find($id);
    
        if (!$investment) {
            throw $this->createNotFoundException('Investment not found');
        }
    
        // Check CSRF token validity
        if ($this->isCsrfTokenValid('delete' . $investment->getId(), $request->request->get('_token'))) {
            $investmentRepository->remove($investment);
            $this->addFlash('success', 'Investment deleted successfully!');
        }
    
        return $this->redirectToRoute('investment_index');
    }
        // Delete an investment
        #[Route('admin/{id}', name: 'investment_delete_admin', methods: ['POST'])]
        public function delete_admin(Request $request, InvestmentRepository $investmentRepository, $id): Response
        {
            // Retrieve the investment entity by ID
            $investment = $investmentRepository->find($id);
        
            if (!$investment) {
                throw $this->createNotFoundException('Investment not found');
            }
        
            // Check CSRF token validity
            if ($this->isCsrfTokenValid('delete' . $investment->getId(), $request->request->get('_token'))) {
                $investmentRepository->remove($investment);
                $this->addFlash('success', 'Investment deleted successfully!');
            }
        
            return $this->redirectToRoute('investment_index_admin');
        }
        #[Route('/investment/{id}/like', name: 'investment_like', methods: ['POST'])]
public function like(int $id, EntityManagerInterface $entityManager, InvestmentRepository $investmentRepository, SessionInterface $session): Response
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

    return $this->redirectToRoute('investment_index_afficher');
}
#[Route('/toggle/{userId}/{postId}', name: 'favorite_toggle', methods: ['POST'])]
    public function toggleFavorite(int $userId, int $postId, UserRepository $userRepository, InvestmentFavoriteRepository $favoriteRepository, EntityManagerInterface $em): JsonResponse
    {
        // Fetch the user by ID
        $user = $userRepository->find($userId);
        $this->logger->info('User attempted to toggle favorite', ['user_id' => $userId, 'post_id' => $postId]);

        if (!$user) {
            $this->logger->error('User not found', ['user_id' => $userId]);
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Fetch the post by ID
        $post = $em->getRepository(Investment::class)->find($postId);
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
        $favorite = new InvestmentFavorite();
        $favorite->setUser($user);
        $favorite->setPost($post);
        $this->logger->info('Adding favorite', ['user_email' => $user->getEmail(), 'post_id' => $post->getId()]);

        $em->persist($favorite);
        $em->flush();

        return new JsonResponse(['status' => 'added']);
    }
}
