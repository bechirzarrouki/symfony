<?php

namespace App\Controller;

use App\Entity\Investment;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\InvestmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/investment')]
class InvestmentController extends AbstractController
{
    // List all investments
    #[Route('/', name: 'investment_index', methods: ['GET'])]
    public function index(InvestmentRepository $investmentRepository): Response
    {
        return $this->render('investisement/index.html.twig', [
            'investments' => $investmentRepository->findAll(),
        ]);
    }
    #[Route('/investisementadmin', name: 'investment_index_admin', methods: ['GET'])]
    public function indexadmin(InvestmentRepository $investmentRepository): Response
    {
        return $this->render('backoffice/investissement/investisementadmin.html.twig', [
            'investments' => $investmentRepository->findAll(),
        ]);
    }
    #[Route('/investisementafficher', name: 'investment_index_afficher', methods: ['GET'])]
    public function indexaffichage(InvestmentRepository $investmentRepository): Response
    {
        return $this->render('investisement/investisement.html.twig', [
            'investments' => $investmentRepository->findAll(),
        ]);
    }

    // Create a new investment using a dummy user if not logged in
    #[Route('/new', name: 'investment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, InvestmentRepository $investmentRepository, EntityManagerInterface $em): Response
    {
        $investment = new Investment();
    
        if ($request->isMethod('POST')) {
            // Retrieve values from the request
            $content = $request->request->get('content');
            $types = $request->request->all('type'); // Ensures type is an array
    
    
            // Set investment values
            $investment->setContent($content);
            $investment->setInvestmentTypes($types);
    
            // Use the logged-in user if available, otherwise use/create a dummy user
            $user = $this->getUser();
            if (!$user) {
                $user = $em->getRepository(User::class)->findOneBy([]) ?? new User();
                if (!$user->getId()) {
                    $user->setName('Dummy Investor');
                    $user->setEmail('dummy@investor.com');
                    $user->setRole('ROLE_INVESTOR');
                    $em->persist($user);
                    $em->flush();
                }
            }
            $investment->setUser($user);
    
            // Save the new investment
            $investmentRepository->save($investment);
            $this->addFlash('success', 'Investment created successfully!');
    
            return $this->redirectToRoute('investment_index');
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
}
