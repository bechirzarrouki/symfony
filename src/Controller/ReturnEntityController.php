<?php

namespace App\Controller;

use App\Entity\ReturnEntity;
use App\Repository\ReturnEntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
}
