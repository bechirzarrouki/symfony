<?php

namespace App\Controller;

use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Cours;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CoursController extends AbstractController
{
    #[Route('/cours', name: 'app_showcours')]
    public function showcours(CoursRepository $rep): Response
    {
        $a = $rep->findAll();
        return $this->render('cours/cours.html.twig', [
            'tabcours' => $a,
        ]);
    }

    #[Route('/admincours', name: 'app_showcoursadmin')]
    public function showcoursadmin(CoursRepository $rep): Response
    {
        $a = $rep->findAll();
        return $this->render('backoffice/coursadmin.html.twig', [
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

    /* #[Route('/addformcours', name: 'app_addformcours', methods: ['POST'])]
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
    }*/


    #[Route('/addformcours', name: 'app_addformcours', methods: ['POST'])]
    public function addFormCours(
        Request $request,
        SluggerInterface $slugger,
        #[Autowire('%cours_pdf_directory%')] string $coursDirectory,
        ManagerRegistry $manager
    ): Response {
        $em = $manager->getManager();
        $cours = new Cours();

        $cours->setNomCours($request->get('NomCours'))
            ->setDescription($request->get('description'))
            ->setDuree($request->get('duree'))
            ->setType($request->get('type'));

        $file = $request->files->get('Filename');
        if ($file) {
            try {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
                $file->move($coursDirectory, $newFilename);
                $cours->setFilename($newFilename);
            } catch (FileException $e) {
                // Log or handle file upload errors
            }
        }

        $em->persist($cours);
        $em->flush();

        return $this->redirectToRoute('app_showcoursadmin');
    }

    /* #[Route('/edit-cours/{id}', name: 'app_editcours', methods: ['GET', 'POST'])]
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

        return $this->render('backoffice/coursadmin.html.twig', [
            'cours'    => $cours,
            'tabcours' => $allCourses,
        ]);
    }*/
    #[Route('/edit-cours/{id}', name: 'app_editcours', methods: ['GET', 'POST'])]
    public function editCours(
        Request $request,
        SluggerInterface $slugger,
        #[Autowire('%cours_pdf_directory%')] string $coursDirectory,
        ManagerRegistry $manager,
        $id
    ): Response {
        // Fetch the course from the database using the ID
        $cours = $manager->getRepository(Cours::class)->find($id);

        // If the course doesn't exist, redirect back to the admin page
        if (!$cours) {
            return $this->redirectToRoute('app_showcoursadmin');
        }

        // Handle the form submission
        if ($request->isMethod('POST')) {
            // Update the course details
            $cours->setNomCours($request->get('NomCours'))
                ->setDescription($request->get('description'))
                ->setDuree($request->get('duree'))
                ->setType($request->get('type'));

            // Handle file upload if a file is provided (no validation here)
            $file = $request->files->get('Filename');
            if ($file) {
                // Generate a safe filename using the SluggerInterface
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                // Move the file to the specified directory
                try {
                    $file->move($coursDirectory, $newFilename);
                    // Set the new filename for the course
                    $cours->setFilename($newFilename);
                } catch (FileException $e) {
                    // Handle file upload error (will be caught by JavaScript)
                    return $this->redirectToRoute('app_editcours', ['id' => $id]);
                }
            }

            // Persist the updated course to the database
            $manager->getManager()->flush();

            // Redirect back to the course list page after updating
            return $this->redirectToRoute('app_showcoursadmin');
        }

        // For GET request, render the admin view with the course data and the full list of courses
        $allCourses = $manager->getRepository(Cours::class)->findAll();

        return $this->render('backoffice/coursadmin.html.twig', [
            'cours'    => $cours,
            'tabcours' => $allCourses,
        ]);
    }



    #[Route('/cours/view/{id}', name: 'app_cours_view')]
    public function viewCours(int $id, ManagerRegistry $manager): Response
    {
        $em = $manager->getManager();
        $cours = $em->getRepository(Cours::class)->find($id);

        if (!$cours) {
            throw $this->createNotFoundException('Cours non trouvé');
        }

        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/courspdf/' . $cours->getFilename();

        if (!file_exists($pdfPath)) {
            throw $this->createNotFoundException('Le fichier PDF est introuvable');
        }

        // Parse the PDF
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $text = nl2br($pdf->getText()); // Convert line breaks to <br>

        return $this->render('cours/view.html.twig', [
            'cours' => $cours,
            'pdfContent' => $text,
        ]);
    }
}
