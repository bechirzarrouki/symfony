<?php

namespace App\Controller;

use App\Entity\ReturnEntity;
use App\Repository\ReturnEntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\InvestmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ReturnEntityController extends AbstractController
{
    private $returnEntityRepository;

    public function __construct(ReturnEntityRepository $returnEntityRepository)
    {
        $this->returnEntityRepository = $returnEntityRepository;
    }

    #[Route('/return-entity', name: 'app_return_entity')]
    public function index(): Response
    {
        // Fetch all ReturnEntities
        $returnEntities = $this->returnEntityRepository->findAll();

        // Example of calling the custom method
        $statusEntities = $this->returnEntityRepository->findByStatus('active');

        return $this->render('return_entity/index.html.twig', [
            'returnEntities' => $returnEntities,
            'statusEntities' => $statusEntities,
        ]);
    }
    #[Route('/investment/{investmentId}/return/new', name: 'return_create')]
    public function createReturnEntity(Request $request, int $investmentId, EntityManagerInterface $em, InvestmentRepository $investmentRepository, SessionInterface $session): Response
    {
        // Fetch the Investment entity using the provided ID
        $investment = $investmentRepository->find($investmentId);
    
        // If the Investment is not found, display an error message
        if (!$investment) {
            $this->addFlash('error', 'Investment not found.');
            return $this->redirectToRoute('investment_new'); // Redirect to the investment creation page
        }
    
        // Retrieve investment data from session (from the first controller)
        $investmentData = $session->get('investmentData');
        
        if (!$investmentData) {
            $this->addFlash('error', 'No investment data found in session.');
            return $this->redirectToRoute('investment_new');
        }
    
        // Create a new ReturnEntity instance
        $returnEntity = new ReturnEntity();
    
        // Set data from the session (the first controller)
        $returnEntity->setDescription($investmentData['Description']);
        $returnEntity->setTypeReturn($investmentData['TypeReturn']); // Assuming 'types' is an array, you can change how you handle it.
        $returnEntity->setTaxRendement($investmentData['taxRendement']);
        $returnEntity->setDateDeadline($investmentData['dateDeadline']);
        $returnEntity->setStatus($investmentData['status']);
    
        // Set the Investment to the ReturnEntity
        $returnEntity->setInvestment($investment);
    
        // Persist the ReturnEntity
        $em->persist($returnEntity);
        $em->flush();  // Save the ReturnEntity to the database
    
        // Flash message for successful return creation
        $this->addFlash('success', 'Return has been created successfully.');
    
        // Redirect to a page where the user can see the investment details or all returns
        return $this->redirectToRoute('investment_index');
    }
    #[Route('/api/investment/{investmentId}/returns', name: 'api_get_returns', methods: ['GET'])]
    public function getReturns(int $investmentId, ReturnEntityRepository $returnRepository, InvestmentRepository $investmentRepository): JsonResponse
    {
        $investment = $investmentRepository->find($investmentId);
        
        if (!$investment) {
            return new JsonResponse(['error' => 'Investment not found'], 404);
        }

        $returns = $returnRepository->findBy(['investment' => $investment]);

        $data = [];
        foreach ($returns as $return) {
            $data[] = [
                'id' => $return->getId(),
                'description' => $return->getDescription(),
                'typeReturn' => $return->getTypeReturn(),
                'taxRendement' => $return->getTaxRendement(),
                'dateDeadline' => $return->getDateDeadline()->format('Y-m-d'),
                'status' => $return->getStatus(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/return/{id}/delete', name: 'return_delete', methods: ['POST'])]
    public function deleteReturn(Request $request, ReturnEntityRepository $returnEntityRepository, EntityManagerInterface $em, $id): Response
    {
        // Retrieve the ReturnEntity by ID
        $returnEntity = $returnEntityRepository->find($id);
    
        // If the ReturnEntity is not found, throw a 404 exception
        if (!$returnEntity) {
            $this->addFlash('error', 'Return not found.');
            return $this->redirectToRoute('investment_index'); // Redirect back to the list
        }
    
            // Remove the ReturnEntity
            $em->remove($returnEntity);
            $em->flush();
            $this->addFlash('success', 'Return has been deleted successfully.');
    
        // Redirect to the return entity list page
        return $this->redirectToRoute('investment_index');
    }

    
    #[Route('/return/{id}/delete/admin', name: 'return_delete_admin', methods: ['POST'])]
    public function deleteReturnadmin(Request $request, ReturnEntityRepository $returnEntityRepository, EntityManagerInterface $em, $id): Response
    {
        // Retrieve the ReturnEntity by ID
        $returnEntity = $returnEntityRepository->find($id);
    
        // If the ReturnEntity is not found, throw a 404 exception
        if (!$returnEntity) {
            $this->addFlash('error', 'Return not found.');
            return $this->redirectToRoute('investment_index_admin'); // Redirect back to the list
        }
    
            // Remove the ReturnEntity
            $em->remove($returnEntity);
            $em->flush();
            $this->addFlash('success', 'Return has been deleted successfully.');
    
        // Redirect to the return entity list page
        return $this->redirectToRoute('investment_index_admin');
    }
    #[Route('/return/{id}/edit', name: 'return_edit', methods: ['POST'])]
    public function editReturn(
        Request $request,
        ReturnEntityRepository $returnEntityRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $returnEntity = $returnEntityRepository->find($id);

        if (!$returnEntity) {
            return $this->redirectToRoute('investment_index_admin');
        }


            $returnEntity->setDescription($request->request->get('description'));
            $returnEntity->setTypeReturn($request->request->get('typeReturn'));
            $returnEntity->setTaxRendement((float)$request->request->get('taxRendement'));
            $returnEntity->setDateDeadline(new \DateTime($request->request->get('dateDeadline')));
            $returnEntity->setStatus($request->request->get('status'));
        $entityManager->persist($returnEntity);
        $entityManager->flush();

        return $this->redirectToRoute('investment_index');
    }
}
