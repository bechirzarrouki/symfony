<?php

/*namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuizRepository;
use App\Repository\CoursRepository;
use App\Entity\Cours;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Smalot\PdfParser\Parser;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Quiz;

class QuizController extends AbstractController
{
    #[Route('/adminquiz', name: 'app_showquizadmin')]
    public function showQuiz(QuizRepository $quizRepository, CoursRepository $coursRepository): Response
    {
        $quizzes = $quizRepository->findAll();
        $courses = $coursRepository->findAll(); // Get all courses

        return $this->render('backoffice/quiz/quizadmin.html.twig', [
            'tabquiz' => $quizzes,
            'tabcours' => $courses, // Pass courses to the template
        ]);
    }
    /*#[Route('/addformquiz', name: 'app_addformquiz', methods: ['POST'])]
    public function addFormQuiz(
        Request $request,
        SluggerInterface $slugger,
        #[Autowire('%quiz_pdf_directory%')] string $quizDirectory,
        ManagerRegistry $manager
    ): Response {
        $em = $manager->getManager();
        $quiz = new Quiz();

        // Set quiz details from form inputs
        $quiz->setQuizName($request->get('quiz_name'));

        // Handle correct answers (assuming it's an array from the form)
        $correctAnswers = $request->get('correct_answers', []);
        if (!is_array($correctAnswers)) {
            $correctAnswers = explode(',', $correctAnswers); // Convert from comma-separated string
        }
        $quiz->setCorrectAnswers($correctAnswers);

        // Retrieve the associated course
        $courseId = $request->get('id_cours');
        $cours = $em->getRepository(Cours::class)->find($courseId);
        if ($cours) {
            $quiz->setIdCours($cours);
        }

        // Handle Quiz PDF upload
        $quizFile = $request->files->get('quiz_pdf');
        if ($quizFile) {
            try {
                $originalFilename = pathinfo($quizFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $quizFile->guessExtension();
                $quizFile->move($quizDirectory, $newFilename);
                $quiz->setQuizPdf($newFilename);
            } catch (FileException $e) {
                // Handle file upload error
            }
        }

        // Handle Quiz Correction PDF upload
        $correctionFile = $request->files->get('quiz_correc_pdf');
        if ($correctionFile) {
            try {
                $originalFilename = pathinfo($correctionFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $correctionFile->guessExtension();
                $correctionFile->move($quizDirectory, $newFilename);
                $quiz->setQuizCorrecPdf($newFilename);
            } catch (FileException $e) {
                // Handle file upload error
            }
        }

        // Persist and save the new quiz
        $em->persist($quiz);
        $em->flush();

        return $this->redirectToRoute('app_showquizadmin');
    }
}*/




/*namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Cours;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class QuizController extends AbstractController
{
    #[Route('/addformquiz', name: 'app_addformquiz', methods: ['POST'])]
    public function addFormQuiz(
        Request $request,
        SluggerInterface $slugger,
        #[Autowire('%cours_pdf_directory%')] string $coursDirectory,
        ManagerRegistry $manager,
        HttpClientInterface $client
    ): Response {
        $em = $manager->getManager();

        // Retrieve the course using its ID from the form
        $courseId = $request->get('id_cours');
        $cours = $em->getRepository(Cours::class)->find($courseId);
        if (!$cours) {
            throw $this->createNotFoundException("Course not found");
        }

        // Get the course PDF filename from the course entity
        $coursePdfFilename = $cours->getFilename(); // Assumes your Cours entity has getFilename()
        if (!$coursePdfFilename) {
            throw $this->createNotFoundException("Course PDF not found");
        }

        // Build the full path to the stored course PDF
        $coursePdfPath = $coursDirectory . '/' . $coursePdfFilename;
        if (!file_exists($coursePdfPath)) {
            throw $this->createNotFoundException("Course PDF file does not exist");
        }

        // Call the external PDFQuiz API to generate a quiz from the course PDF
        $response = $client->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=AIzaSyBf3Pk1lyrSO_Y0dxTld0ap7c4XLtwOpbQ', [
            'headers' => [
                'Authorization' => 'Bearer AIzaSyBf3Pk1lyrSO_Y0dxTld0ap7c4XLtwOpbQ', // Replace with your actual API token
            ],
            'body' => [
                'file' => fopen($coursePdfPath, 'r'),
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw $this->createNotFoundException("Quiz generation failed: " . $response->getStatusCode());
        }

        $apiData = $response->toArray();
        // Expected API response keys: 'quiz_title', 'quiz_pdf_url', 'correct_answers', optionally 'api_quiz_id'

        // Create a new Quiz entity using the API-generated data
        $quiz = new Quiz();
        $quiz->setQuizName($apiData['quiz_title'] ?? 'Generated Quiz')
            ->setQuizPdfUrl($apiData['quiz_pdf_url'] ?? '')
            ->setCorrectAnswers($apiData['correct_answers'] ?? []);
        $quiz->setIdCours($cours);
        if (isset($apiData['api_quiz_id'])) {
            $quiz->setApiQuizId($apiData['api_quiz_id']);
        }

        $em->persist($quiz);
        $em->flush();

        return $this->redirectToRoute('app_showquizadmin');
    }
}*/
