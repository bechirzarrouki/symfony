<?php

namespace App\Controller;

use App\Entity\Requirement;
use App\Repository\RequirementRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class RequirementController extends AbstractController
{
    private $requirementRepository;

    public function __construct(RequirementRepository $requirementRepository)
    {
        $this->requirementRepository = $requirementRepository;
    }

    #[Route('/requirement', name: 'app_requirement')]
    public function index(): Response
    {
        // Fetch all Requirements
        $requirements = $this->requirementRepository->findAll();

        return $this->render('requirement/index.html.twig', [
            'requirements' => $requirements,
        ]);
    }

    #[Route('/project/{projectId}/requirement/new', name: 'requirement_create')]
    public function createRequirement(int $projectId,String $title,String $description,  EntityManagerInterface $em, ProjectRepository $projectRepository): Response
    {
        // Fetch the Project entity using the provided ID
        $project = $projectRepository->find($projectId);

        // If the Project is not found, display an error message
        if (!$project) {
            $this->addFlash('error', 'Project not found.');
            return $this->redirectToRoute('project_index'); // Redirect to the project listing page
        }

        // Create a new Requirement instance
        $requirement = new Requirement();

        // Set data from the form request
        $requirement->setTitle($title);
        $requirement->setDescription($description);

        // Set the Project to the Requirement
        $requirement->setProject($project);

        // Persist the Requirement
        $em->persist($requirement);
        $em->flush();  // Save the Requirement to the database

        // Flash message for successful requirement creation
        $this->addFlash('success', 'Requirement has been created successfully.');

        // Redirect to a page where the user can see the project details or all requirements
        return $this->redirectToRoute('project_show', ['id' => $projectId]);
    }

    #[Route('/api/project/{projectId}/requirements', name: 'api_get_requirements', methods: ['GET'])]
    public function getRequirements(int $projectId, RequirementRepository $requirementRepository, ProjectRepository $projectRepository): JsonResponse
    {
        $project = $projectRepository->find($projectId);

        if (!$project) {
            return new JsonResponse(['error' =>$project], 404);
        }

        $requirements = $requirementRepository->findBy(['project' => $project]);

        $data = [];
        foreach ($requirements as $requirement) {
            $data[] = [
                'id' => $requirement->getId(),
                'title' => $requirement->getTitle(),
                'description' => $requirement->getDescription(),
                'project' => $requirement->getProject()->getId(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/requirement/{id}/delete', name: 'requirement_delete', methods: ['POST'])]
    public function deleteRequirement(Request $request, RequirementRepository $requirementRepository, EntityManagerInterface $em, $id): Response
    {
        // Retrieve the Requirement by ID
        $requirement = $requirementRepository->find($id);

        // If the Requirement is not found, throw a 404 exception
        if (!$requirement) {
            $this->addFlash('error', 'Requirement not found.');
            return $this->redirectToRoute('project_index'); // Redirect back to the list
        }

        // Remove the Requirement
        $em->remove($requirement);
        $em->flush();
        $this->addFlash('success', 'Requirement has been deleted successfully.');

        // Redirect to the project details page
        return $this->redirectToRoute('project_index');
    }

    #[Route('/requirement/{id}/edit', name: 'requirement_edit', methods: ['POST'])]
    public function editRequirement(Request $request, RequirementRepository $requirementRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $requirement = $requirementRepository->find($id);

        if (!$requirement) {
            return $this->redirectToRoute('project_index');
        }

        // Update the Requirement fields
        $requirement->setTitle($request->request->get('pri'));
        $requirement->setDescription($request->request->get('des'));

        $entityManager->persist($requirement);
        $entityManager->flush();

        return $this->redirectToRoute('project_index');
    }
}
