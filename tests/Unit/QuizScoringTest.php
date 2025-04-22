<?php

namespace Tests\Unit;

use App\Models\{Quiz, Question, Answer};
use App\Services\QuizScoringService;
use PHPUnit\Framework\TestCase;

class QuizScoringTest extends TestCase
{
    protected QuizScoringService $quizScoringService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->quizScoringService = new QuizScoringService();
    }

    public function test_calculate_score_with_correct_answers()
    {
        $questions = [
            new Question(['id' => 1, 'correct_answer_id' => 1]),
            new Question(['id' => 2, 'correct_answer_id' => 2]),
        ];

        $userAnswers = [
            ['question_id' => 1, 'answer_id' => 1],
            ['question_id' => 2, 'answer_id' => 2],
        ];

        $score = $this->quizScoringService->calculateScore($questions, $userAnswers);

        $this->assertEquals(100, $score);
    }   
}