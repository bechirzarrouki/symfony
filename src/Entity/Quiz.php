<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $quiz_name = null;

    // Instead of storing local file names, we now store the URL returned by the API
    #[ORM\Column(length: 255)]
    private ?string $quiz_pdf_url = null;



    #[ORM\Column(type: 'json')]
    private array $correct_answers = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $id_cours = null;

    // Optional: If the API provides an identifier for the generated quiz, you can store it:
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $api_quiz_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuizName(): ?string
    {
        return $this->quiz_name;
    }

    public function setQuizName(string $quiz_name): static
    {
        $this->quiz_name = $quiz_name;
        return $this;
    }

    public function getQuizPdfUrl(): ?string
    {
        return $this->quiz_pdf_url;
    }

    public function setQuizPdfUrl(string $quiz_pdf_url): static
    {
        $this->quiz_pdf_url = $quiz_pdf_url;
        return $this;
    }

    public function getCorrectAnswers(): array
    {
        return $this->correct_answers;
    }

    public function setCorrectAnswers(array $correct_answers): static
    {
        $this->correct_answers = $correct_answers;
        return $this;
    }

    public function getIdCours(): ?Cours
    {
        return $this->id_cours;
    }

    public function setIdCours(?Cours $id_cours): static
    {
        $this->id_cours = $id_cours;
        return $this;
    }

    public function getApiQuizId(): ?string
    {
        return $this->api_quiz_id;
    }

    public function setApiQuizId(?string $api_quiz_id): static
    {
        $this->api_quiz_id = $api_quiz_id;
        return $this;
    }
}
