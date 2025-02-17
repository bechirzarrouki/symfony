<?php

namespace App\Controller;

use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Cours;

class CoursController extends AbstractController
{
    #[Route('/cours', name: 'app_showcours')]
    public function showcours(CoursRepository $rep): Response
    {
        $a = $rep->findAll();
        return $this->render('front/cours/cours.html.twig', [
            'tabcours' => $a,
        ]);
    }

    #[Route('/admincours', name: 'app_showcoursadmin')]
    public function showcoursadmin(CoursRepository $rep): Response
    {
        $a = $rep->findAll();
        return $this->render('back/coursadmin.html.twig', [
            'tabcours' => $a,
        ]);
    }

    #[Route('/deletecours/{id}', name: 'app_deletecours')]
    public function deleteformjoueur($id, ManagerRegistry $manager, Request $req, CoursRepository $rep): Response
    {
        $em = $manager->getManager();
        $cours = $rep->find($id);

        $em->remove($cours);
        $em->flush();

        return $this->redirect('/admincours');
    }

    #[Route('/addformcours', name: 'app_addformcours', methods: ['POST'])]
    public function addFormCours(ManagerRegistry $manager, Request $req): Response
    {
        $em = $manager->getManager();

        // Create a new course object
        $cours = new Cours();

        // Retrieve form data directly from the request
        $cours->setNomCours($req->get('NomCours'))
            ->setDescription($req->get('description'))
            ->setDuree($req->get('duree'))
            ->setType($req->get('type'));

        // Persist the new course to the database
        $em->persist($cours);
        $em->flush();

        // Redirect back to the course list page
        return $this->redirectToRoute('app_showcoursadmin');
    }

    #[Route('/edit-cours/{id}', name: 'app_editcours', methods: ['GET', 'POST'])]
    public function editCours(ManagerRegistry $manager, Request $request, $id): Response
    {
        // Fetch the course from the database using the ID
        $cours = $manager->getRepository(Cours::class)->find($id);

        // If the course doesn't exist, redirect back to the admin page
        if (!$cours) {
            return $this->redirectToRoute('app_showcoursadmin');
        }

        // If it's a POST request, update the course
        if ($request->isMethod('POST')) {
            $cours->setNomCours($request->get('NomCours'))
                ->setDescription($request->get('description'))
                ->setDuree($request->get('duree'))
                ->setType($request->get('type'));

            $manager->getManager()->flush();

            // Redirect back to the course list page after updating
            return $this->redirectToRoute('app_showcoursadmin');
        }

        // For GET request, render the admin view with the course data and the full list of courses
        $allCourses = $manager->getRepository(Cours::class)->findAll();

        return $this->render('back/coursadmin.html.twig', [
            'cours'    => $cours,
            'tabcours' => $allCourses,
        ]);
    }
}
