<?php

namespace Tests\Feature;

use App\Models\{User, Quiz, Question, Answer, Result, UserAnswer};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizScoringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->quiz = Quiz::factory()->create();
        $this->questions = Question::factory()->count(5)->create(['quiz_id' => $this->quiz->id]);
        $this->answers = Answer::factory()->count(10)->create(['question_id' => $this->questions[0]->id]);
    }

    // public function test_calculate_score_with_correct_answers()
    // {
    //     $userAnswers = [
    //         ['question_id' => $this->questions[0]->id, 'answer_id' => $this->answers[0]->id],
    //         ['question_id' => $this->questions[1]->id, 'answer_id' => $this->answers[1]->id],
    //         ['question_id' => $this->questions[2]->id, 'answer_id' => $this->answers[2]->id],
    //         ['question_id' => $this->questions[3]->id, 'answer_id' => $this->answers[3]->id],
    //         ['question_id' => $this->questions[4]->id, 'answer_id' => $this->answers[4]->id],
    //     ];

    //     foreach ($userAnswers as $userAnswer) {
    //         UserAnswer::create([
    //             'user_id' => $this->user->id,
    //             'quiz_id' => $this->quiz->id,
    //             'question_id' => $userAnswer['question_id'],
    //             'answer_id' => $userAnswer['answer_id'],
    //         ]);
    //     }

    //     // Tính điểm
    //     $score = 0;
    //     foreach ($userAnswers as $userAnswer) {
    //         if ($userAnswer['answer_id'] == Answer::where('question_id', $userAnswer['question_id'])->first()->is_correct) {
    //             $score++;
    //         }
    //     }

    //     // Lưu kết quả
    //     Result::create([
    //         'user_id' => $this->user->id,
    //         'quiz_id' => $this->quiz->id,
    //         'score' => ($score / count($userAnswers)) * 100,
    //     ]);

    //     // Kiểm tra kết quả
    //     $result = Result::where('user_id', $this->user->id)->where('quiz_id' => $this->quiz->id)->first();
    //     $this->assertEquals(($score / count($userAnswers)) * 100, $result->score);
    // }

    public function test_time_taken_is_saved_correctly()
    {
        $this->actingAs($this->user);

        $answers = [
            $this->questions[0]->id => $this->answers[0]->id,
            $this->questions[1]->id => $this->answers[1]->id,
        ];

        $timeTaken = 120; // Simulate 2 minutes

        $response = $this->post(route('quizz.submit', $this->quiz->id), [
            'answers' => $answers,
            'time_taken' => $timeTaken,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('results', [
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->user->id,
            'time_taken' => $timeTaken,
        ]);
    }
}
