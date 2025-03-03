<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Cours;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\QuizRepository;
use App\Repository\CoursRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Service\CertificateGenerator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class QuizController extends AbstractController
{

    #[Route('/quiz', name: 'app_quiz')]
    public function showQuiz(QuizRepository $rep, CoursRepository $coursRepository): Response
    {
        $quizzes = $rep->findAll();
        $courses = $coursRepository->findAll(); // Get all courses
        return $this->render('backoffice/quiz/quizadmin.html.twig', [
            'tabquizzes' => $quizzes,
            'courses'  => $courses, // Pass courses to the template
        ]);
    }

    #[Route('/deletequiz/{id}', name: 'app_deletequiz', methods: ['GET'])]
    public function deleteQuiz(QuizRepository $quizRepository, EntityManagerInterface $em, int $id): Response
    {
        $quiz = $quizRepository->find($id);

        if (!$quiz) {
            throw $this->createNotFoundException('Quiz not found');
        }

        $cours = $quiz->getIdCours(); // Get the related course before deletion

        $em->remove($quiz);
        $em->flush();

        $this->addFlash('success', 'Quiz deleted successfully.');

        // Redirect back to the course view, ensuring 'cours' is passed
        return $this->redirectToRoute('app_cours_adminview', ['id' => $cours->getId()]);
    }

    #[Route('/addquiz', name: 'app_addquiz', methods: ['POST'])]
    public function addQuiz(ManagerRegistry $manager, Request $request, CoursRepository $coursRepository): Response
    {
        $em = $manager->getManager();
        $quiz = new Quiz();

        // Set title for the quiz
        $quiz->setTitle($request->get('title'));

        // Get the course ID from the form, then fetch the Cours entity
        $coursId = $request->get('IdCours');
        $cours = $coursRepository->find($coursId);
        if (!$cours) {
            $this->addFlash('error', 'Invalid course selected.');
            return $this->redirectToRoute('app_quiz');
        }
        $quiz->setIdCours($cours);

        // Get and format questions
        $questions = [];
        foreach ($request->get('questions') as $questionData) {
            // Process the answers to handle commas and split them properly
            $answersInput = $questionData['answers'];
            $answersArray = array_map('trim', explode('|', $answersInput));  // Split answers by '|' and trim them

            $questions[] = [
                'question'   => $questionData['question'],
                'answers'    => $answersArray,  // Store answers as an array
                'correct'    => $questionData['correct'],
                'userAnswer' => null  // Initial state for user's answer
            ];
        }
        $quiz->setQuestions($questions);

        // Persist the quiz object
        $em->persist($quiz);
        $em->flush();

        // Redirect back to the course view after quiz creation
        return $this->redirectToRoute('app_cours_adminview', ['id' => $cours->getId()]);
    }


    #[Route('/takequiz/{id}', name: 'app_takequiz', methods: ['GET', 'POST'])]
    public function takeQuiz(Request $request, QuizRepository $quizRepository, ManagerRegistry $manager, int $id, CertificateGenerator $certificateGenerator): Response
    {
        $em = $manager->getManager();
        $quiz = $quizRepository->find($id);

        if (!$quiz) {
            throw $this->createNotFoundException('Quiz not found');
        }

        $score = null; // Default null before submission
        $userAnswers = [];
        $fullScore = false; // Flag to check if user got a full score

        if ($request->isMethod('POST')) {
            $userAnswers = $request->get('answers', []);
            $questions = $quiz->getQuestions();
            $score = 0;

            // Loop through questions and compare answers
            foreach ($questions as $index => $question) {
                if (isset($userAnswers[$index]) && $userAnswers[$index] === $question['correct']) {
                    $score++; // Increase score if correct
                }
            }

            // Save score (optional: if you want to track it)
            $quiz->setScore($score);
            $em->flush();

            $this->addFlash('success', "Quiz submitted! Your score: $score / " . count($questions));

            // Check if the user got a full score
            if ($score === count($questions)) {
                $fullScore = true;
            }
            // If full score and the user requests the certificate
            /*  if ($fullScore && $request->query->get('generate_certificate')) {
                // Prepare data for certificate
                $websiteName = 'InnovMatch'; // Replace with your actual website name
                $courseTitle = $quiz->getTitle();
                $courseDetails = 'Course details go here. Add any relevant information about the course.';

                // Define path to save the certificate in the public/certif folder
                $outputPath = $this->getParameter('kernel.project_dir') . '/public/certif/certificate_' . $quiz->getId() . '.pdf';

                // Generate the certificate PDF
                $certificateGenerator->generateCertificate($websiteName, $courseTitle, $courseDetails, $outputPath);

                // Return the generated certificate as a downloadable file
                return $this->file($outputPath);
            }*/
        }

        return $this->render('cours/quiz.html.twig', [
            'quiz' => $quiz,
            'score' => $score,
            'userAnswers' => $userAnswers,
            'fullScore' => $fullScore,

            'cours' => $quiz->getIdCours()
        ]);
    }
    #[Route('/editquiz/{id}', name: 'app_editquiz', methods: ['GET', 'POST'])]
    public function editQuiz($id, ManagerRegistry $manager, Request $request, CoursRepository $coursRepository, QuizRepository $quizRepository): Response
    {
        $em = $manager->getManager();

        // Fetch the existing quiz by ID
        $quiz = $quizRepository->find($id);
        if (!$quiz) {
            $this->addFlash('error', 'Quiz not found.');
            return $this->redirectToRoute('app_quiz');
        }

        // Ensure $cours is always defined
        $cours = $quiz->getIdCours(); // Get the existing course

        // Check if a new course ID was provided
        $coursId = $request->get('IdCours');
        if ($coursId) {
            $newCours = $coursRepository->find($coursId);
            if (!$newCours) {
                throw new \Exception("Invalid course ID: $coursId");
            }
            $quiz->setIdCours($newCours);
            $cours = $newCours; // Update cours to avoid undefined error
        }

        // Handle form submission
        if ($request->isMethod('POST')) {
            $quiz->setTitle($request->get('title'));

            // Get and format updated questions
            $questions = [];
            foreach ($request->get('questions') as $questionData) {
                $answersInput = $questionData['answers'];
                $answersArray = array_map('trim', explode('|', $answersInput));

                $questions[] = [
                    'question'   => $questionData['question'],
                    'answers'    => $answersArray,
                    'correct'    => $questionData['correct'],
                    'userAnswer' => null,
                ];
            }
            $quiz->setQuestions($questions);

            // Save the quiz
            $em->flush();

            // Redirect back to the course view after quiz update
            return $this->redirectToRoute('app_cours_adminview', ['id' => $quiz->getIdCours()->getId()]);
        }

        // If GET request, render the form to edit the quiz
        /*return $this->render('backoffice/viewadmin.html.twig', [
            'quiz' => $quiz,
            'cours' => $cours // ✅ Now $cours is always defined
        ]);*/
        return $this->redirectToRoute('app_cours_adminview', ['id' => $quiz->getIdCours()->getId()]);
    }
    /* #[Route('/generate-certificate/{id}', name: 'app_generate_certificate', methods: ['GET'])]
    public function generateCertificate(int $id, QuizRepository $quizRepository, CertificateGenerator $certificateGenerator, ParameterBagInterface $params): Response
    {
        $quiz = $quizRepository->find($id);

        if (!$quiz) {
            throw $this->createNotFoundException('Quiz not found');
        }

        $websiteName = 'InnovMatch';
        $courseTitle = $quiz->getTitle();
        $courseDetails = 'Course details go here. Add any relevant information about the course.';

        // Récupérer le dossier des certificats depuis les paramètres
        $certificateDirectory = $params->get('certificate_directory');
        $outputPath = $certificateDirectory . '/certificate_' . $quiz->getId() . '.pdf';

        // Générer le certificat
        $certificateGenerator->generateCertificate($websiteName, $courseTitle, $courseDetails, $outputPath);

        // Retourner le fichier en téléchargement
        return $this->file($outputPath);
    }*/
    #[Route('/generate-certificate/{id}', name: 'app_generate_certificate', methods: ['GET'])]
    public function generateCertificate(int $id, QuizRepository $quizRepository, CertificateGenerator $certificateGenerator, ParameterBagInterface $params): Response
    {
        $quiz = $quizRepository->find($id);

        if (!$quiz) {
            throw $this->createNotFoundException('Quiz not found');
        }

        $cours = $quiz->getIdCours(); // Retrieve the associated course

        if (!$cours) {
            throw $this->createNotFoundException('Associated course not found');
        }

        $websiteName = 'InnovMatch';
        $courseTitle = $quiz->getTitle();
        $courseDetails = $cours->getDescription(); // Get the course description

        // Récupérer le dossier des certificats depuis les paramètres
        $certificateDirectory = $params->get('certificate_directory');
        $outputPath = $certificateDirectory . '/certificate_' . $quiz->getId() . '.pdf';

        // Générer le certificat
        $certificateGenerator->generateCertificate($websiteName, $courseTitle, $courseDetails, $outputPath);

        // Retourner le fichier en téléchargement
        return $this->file($outputPath);
    }
}
