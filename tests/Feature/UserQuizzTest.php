<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Answer;
use App\Models\Question;
use Tests\TestCase;

class UserQuizzTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'user']);
    }

    /** @test user can see list of public quizzes on home */
    public function test_user_can_see_public_quizzes_on_home()
    {
        $this->actingAs($this->user);

        Quiz::factory()->create([
            'name' => 'Public Quiz',
            'description' => 'This is a public quiz.',
            'is_public' => true,
            'created_by' => $this->user->id,
        ]);

        $response = $this->get('/home');
        $response->assertStatus(200);
        $response->assertSee('Public Quiz');
    }

    /** @test user can view quiz with questions */
    public function test_user_can_view_quiz_with_questions()
    {
        $this->actingAs($this->user);

        $quiz = Quiz::factory()->create([
            'name' => 'Quiz with Questions',
            'description' => 'This quiz has questions.',
            'is_public' => true,
            'created_by' => $this->user->id,
        ]);

        $question = Question::factory()->create([
            'quiz_id' => $quiz->id,
            'question' => 'What is the capital of France?',
        ]);

        $response = $this->get('/quizz/' . $quiz->id);
        $response->assertStatus(200);
        $response->assertSee('What is the capital of France?');
    }

    /** @test user can submit quiz and redirected to result */
    public function test_user_can_submit_quiz_and_redirect_to_result()
    {
        $this->actingAs($this->user);

        $quiz = Quiz::factory()->create([
            'name' => 'Quiz to Submit',
            'description' => 'This quiz can be submitted.',
            'is_public' => true,
            'created_by' => $this->user->id,
        ]);

        $question = Question::factory()->create([
            'quiz_id' => $quiz->id,
            'question' => 'What is the capital of France?',
        ]);

        $answer = Answer::factory()->create([
            'question_id' => $question->id,
            'answer' => 'Paris',
            'is_correct' => true,
        ]);

        $response = $this->post('/quizz/' . $quiz->id . '/submit', [
            'answers' => [
                $question->id => $answer->id,
            ],
            'time_taken' => 60,
        ]);

        // Lấy kết quả mới nhất của user cho quiz này
        $result = \App\Models\Result::where('user_id', $this->user->id)
            ->where('quiz_id', $quiz->id)
            ->latest('id')->first();

        $response->assertStatus(302);
        $response->assertRedirect('/quizz/' . $result->id . '/result');
    }

    /** @test user cannot access private or non-existent quiz */
    public function test_user_cannot_access_private_or_nonexistent_quiz()
    {
        $this->actingAs($this->user);

        $quiz = Quiz::factory()->create([
            'name' => 'Private Quiz',
            'description' => 'This quiz is private.',
            'is_public' => false,
            'created_by' => $this->user->id,
        ]);

        $response = $this->get('/quizz/' . $quiz->id);
        $response->assertStatus(403);

        $response = $this->get('/quizz/9999');
        $response->assertStatus(404);
    }
}