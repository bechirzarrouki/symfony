<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $IdCours = null;
    /* #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;*/

    /**
     * Stores the questions, answers, correct answer, and user answer.
     * Each question is an array with:
     * - 'question' => 'Question text',
     * - 'answers' => ['Answer 1', 'Answer 2', 'Answer 3', 'Answer 4'],
     * - 'correct' => 'Correct answer',
     * - 'userAnswer' => null // Updated when the user answers.
     */
    #[ORM\Column(type: 'json')]
    private array $questions = [];

    #[ORM\Column(type: 'integer')]
    private int $score = 0; // Stores the user's score.

    // Getters and Setters for all the properties
    /*public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }*/
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getIdCours(): ?Cours
    {
        return $this->IdCours;
    }

    public function setIdCours(?Cours $IdCours): static
    {
        $this->IdCours = $IdCours;

        return $this;
    }
    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function setQuestions(array $questions): self
    {
        $this->questions = $questions;
        return $this;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;
        return $this;
    }

    // Add a question with its answers and correct answer
    public function addQuestion(string $question, array $answers, string $correctAnswer): self
    {
        $this->questions[] = [
            'question'   => $question,
            'answers'    => $answers,
            'correct'    => $correctAnswer,
            'userAnswer' => null // Initially null, to be updated when the user answers
        ];
        return $this;
    }

    // Set the user's answer for a specific question
    public function setUserAnswer(int $questionIndex, string $userAnswer): self
    {
        if (isset($this->questions[$questionIndex])) {
            $this->questions[$questionIndex]['userAnswer'] = $userAnswer;
        }
        return $this;
    }

    // Calculate the user's score
    public function calculateScore(): self
    {
        $this->score = 0;
        foreach ($this->questions as $question) {
            if ($question['userAnswer'] === $question['correct']) {
                $this->score++;  // Increment score if the answer is correct
            }
        }
        return $this;
    }
    // In App\Entity\Quiz

    public function getCorrectAnswers(): array
    {
        return array_map(function ($question) {
            return $question['correct'];  // Returns the correct answer for each question
        }, $this->questions);
    }
}
