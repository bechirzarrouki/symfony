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
use App\Entity\Quiz;

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
        #[Autowire('%cours_image_directory%')] string $imageDirectory,
        ManagerRegistry $manager
    ): Response {
        $em = $manager->getManager();
        $cours = new Cours();

        // Set standard text fields and new fields from the request
        $cours->setNomCours($request->get('NomCours'))
            ->setDescription($request->get('description'))
            ->setDuree($request->get('duree'))
            ->setType($request->get('type'))
            ->setObjectifs($request->get('objectifs'));
        $date = \DateTime::createFromFormat('Y-m-d', $request->get('date'));
        $cours->setDate($date);


        // Handle PDF file upload for the "Filename" field
        $pdfFile = $request->files->get('Filename');
        if ($pdfFile) {
            try {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();
                $pdfFile->move($coursDirectory, $newFilename);
                $cours->setFilename($newFilename);
            } catch (FileException $e) {
                // Log or handle file upload errors for PDF
            }
        }

        // Handle image file upload for the "image" field
        $imageFile = $request->files->get('image');
        if ($imageFile) {
            try {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newImageFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($imageDirectory, $newImageFilename);
                $cours->setImage($newImageFilename);
            } catch (FileException $e) {
                // Log or handle file upload errors for image
            }
        } else {
            // Optionally set a default or leave as null
            $cours->setImage(null);
        }

        $em->persist($cours);
        $em->flush();

        return $this->redirectToRoute('app_showcoursadmin');
    }
    #[Route('/edit-cours/{id}', name: 'app_editcours', methods: ['GET', 'POST'])]
    public function editCours(
        Request $request,
        SluggerInterface $slugger,
        #[Autowire('%cours_pdf_directory%')] string $coursDirectory,
        #[Autowire('%cours_image_directory%')] string $imageDirectory,
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
            // Update course details
            $cours->setNomCours($request->get('NomCours'))
                ->setDescription($request->get('description'))
                ->setDuree($request->get('duree'))
                ->setType($request->get('type'))
                ->setObjectifs($request->get('objectifs'));
            $date = \DateTime::createFromFormat('Y-m-d', $request->get('date'));
            $cours->setDate($date);

            // Handle PDF file upload if a new PDF is provided
            $file = $request->files->get('Filename');
            if ($file) {
                // Check if the course already has an old file and delete it
                if ($cours->getFilename()) {
                    $oldFilepath = $coursDirectory . '/' . $cours->getFilename();
                    if (file_exists($oldFilepath)) {
                        unlink($oldFilepath); // Delete the old file
                    }
                }

                // Generate a safe filename for the new PDF
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                // Move the new file to the specified directory
                try {
                    $file->move($coursDirectory, $newFilename);
                    // Set the new filename for the course
                    $cours->setFilename($newFilename);
                } catch (FileException $e) {
                    // Handle file upload error (can be caught by JS)
                    return $this->redirectToRoute('app_editcours', ['id' => $id]);
                }
            }

            // Handle image file upload if a new image is provided
            $imageFile = $request->files->get('image');
            if ($imageFile) {
                // Check if the course already has an old image and delete it
                if ($cours->getImage()) {
                    $oldImagepath = $imageDirectory . '/' . $cours->getImage();
                    if (file_exists($oldImagepath)) {
                        unlink($oldImagepath); // Delete the old image
                    }
                }

                // Generate a safe filename for the new image
                $originalImageFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImageFilename = $slugger->slug($originalImageFilename);
                $newImageFilename = $safeImageFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Move the new image to the specified directory
                try {
                    $imageFile->move($imageDirectory, $newImageFilename);
                    // Set the new image filename for the course
                    $cours->setImage($newImageFilename);
                } catch (FileException $e) {
                    // Handle file upload error for image
                    // You can log the error or return a message
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

        // Fetch quizzes related to the course
        $quizRepository = $manager->getRepository(Quiz::class);
        $quizzes = $quizRepository->findBy(['IdCours' => $cours]);

        return $this->render('cours/view.html.twig', [
            'cours' => $cours,
            'pdfContent' => $text,
            'quizzes' => $quizzes,  // Pass quizzes to the template
        ]);
    }


    #[Route('/admincours/viewadmin/{id}', name: 'app_cours_adminview')]
    public function viewCoursadmin(int $id, ManagerRegistry $manager): Response
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

        // Fetch quizzes related to the course
        $quizRepository = $manager->getRepository(Quiz::class);
        $quizzes = $quizRepository->findBy(['IdCours' => $cours]);

        return $this->render('backoffice/viewadmin.html.twig', [
            'cours' => $cours,
            'pdfContent' => $text,
            'quizzes' => $quizzes,  // Pass quizzes to the template
        ]);
    }
    #[Route('/search-cours', name: 'searchcours')]
    public function search(Request $request, CoursRepository $coursRepository): Response
    {
        // Get the search and type filters from the query string
        $search = $request->query->get('search');
        $type = $request->query->get('type');

        // Start building the query with CoursRepository
        $queryBuilder = $coursRepository->createQueryBuilder('c');

        // Apply search filter if the 'search' parameter exists
        if ($search) {
            $queryBuilder->andWhere('c.NomCours LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Apply type filter if the 'type' parameter exists
        if ($type) {
            $queryBuilder->andWhere('c.type = :type')
                ->setParameter('type', $type);
        }

        // Execute the query and get filtered results
        $tabcours = $queryBuilder->getQuery()->getResult();

        // Render the filtered courses
        return $this->render('cours/cours.html.twig', [
            'tabcours' => $tabcours,
        ]);
    }
}
