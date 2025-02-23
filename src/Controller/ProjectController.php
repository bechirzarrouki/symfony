<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Controller\RequirementController;
use App\Entity\Requirement;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project')]
class ProjectController extends AbstractController
{
    // List all projects
    #[Route('/', name: 'project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    // Create a new project
    #[Route('/new', name: 'project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProjectRepository $projectRepository, EntityManagerInterface $em,RequirementController $rc): Response
    {
        $project = new Project();
    
        if ($request->isMethod('POST')) {
            // Retrieve values from the request
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $budget = (float) $request->request->get('budget');
            $status = $request->request->get('status');
    
            // Set project values
            $project->setTitle($title);
            $project->setDescription($description);
            $project->setBudget($budget);
            $project->setStatus("test");
            
            // Use the logged-in user if available, otherwise use/create a dummy user
            $user = $this->getUser();
            if (!$user) {
                $user = $em->getRepository(User::class)->findOneBy([]) ?? new User();
                if (!$user->getId()) {
                    $user->setName('Dummy Owner');
                    $user->setEmail('dummy@owner.com');
                    $user->setRole('ROLE_COMPANY');
                    $em->persist($user);
                    $em->flush();
                }
            }
            $project->setOwner($user);
    
            // Save the new project
            $projectRepository->save($project);
            $title = $request->request->get('pri');
            $description = $request->request->get('des');
            $requirement=$rc->createRequirement($project->getId(),$title,$description,$em,$projectRepository);
            $this->addFlash('success', 'Project created successfully!');
    
            return $this->redirectToRoute('project_index');
        }
    
        return $this->render('project/new.html.twig', [
            'project' => $project,
        ]);
    }
    
    // Display a single project
    #[Route('/{id}', name: 'project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    // Edit an existing project
    #[Route('/{id}/edit', name: 'project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, ProjectRepository $projectRepository): Response
    {
        // Fetch the Project entity by its ID
        $project = $projectRepository->find($id);

        // If the project is not found, throw a 404 error
        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        // Handle the form submission
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $budget = (float) $request->request->get('budget');
            $status = $request->request->get('status');
    
            // Update the project entity
            $project->setTitle($title);
            $project->setDescription($description);
            $project->setBudget($budget);
            $project->setStatus("status");
    
            // Save the updated entity
            $projectRepository->save($project);
    
            // Add a flash message
            $this->addFlash('success', 'Project updated successfully!');
    
            // Redirect to the project index page
            return $this->redirectToRoute('project_index');
        }

        // Render the edit form
        return $this->render('project/edit.html.twig', [
            'project' => $project,
        ]);
    }

    // Delete a project
    #[Route('/{id}', name: 'project_delete', methods: ['POST'])]
    public function delete(Request $request, ProjectRepository $projectRepository, $id): Response
    {
        // Retrieve the project entity by ID
        $project = $projectRepository->find($id);
    
        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }
    
        // Check CSRF token validity
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            $projectRepository->remove($project);
            $this->addFlash('success', 'Project deleted successfully!');
        }
    
        return $this->redirectToRoute('project_index');
    }
}
