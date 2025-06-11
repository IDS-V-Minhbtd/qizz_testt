<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Answer;
use App\Models\Question;

class AdminQuizzTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test admin can see quizzes */
    public function test_admin_can_see_quizzes()
    {
        $this->actingAs($this->user);
        $this->user->update(['role' => 'admin']);
        $response = $this->get('/admin/quizzes');  
        $response->assertStatus(200);
    }

 
    /** @test admin can edit quiz */
    public function test_admin_can_edit_quiz()
    {
        $this->actingAs($this->user);
        $this->user->update(['role' => 'admin']);

        $quiz = Quiz::factory()->create([
            'name' => 'Test Quiz',
            'description' => 'This is a test quiz.',
        ]);

        $response = $this->put('/admin/quizzes/' . $quiz->id, [  
            'name' => 'Updated Quiz',
            'description' => 'This is an updated test quiz.',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/quizzes');
        $this->assertDatabaseHas('quizzes', [
            'name' => 'Updated Quiz',
            'description' => 'This is an updated test quiz.',
        ]);
    }

    /** @test admin can delete quiz */
    public function test_admin_can_delete_quiz()
    {
        $this->actingAs($this->user);
        $this->user->update(['role' => 'admin']);

        $quiz = Quiz::factory()->create([
            'name' => 'Test Quiz',
            'description' => 'This is a test quiz.',
        ]);

        $response = $this->delete('/admin/quizzes/' . $quiz->id);  
        $response->assertStatus(302);
        $response->assertRedirect('/admin/quizzes');
        $this->assertDatabaseMissing('quizzes', [
            'name' => 'Test Quiz',
            'description' => 'This is a test quiz.',
        ]);
    }

    /** @test admin can see quiz */
    public function test_admin_can_see_quiz()
    {
        $this->actingAs($this->user);
        $this->user->update(['role' => 'admin']);

        $quiz = Quiz::factory()->create([
            'name' => 'Test Quiz',
            'description' => 'This is a test quiz.',
        ]);

        $response = $this->get('/admin/quizzes');  
        $response->assertStatus(200);
        $response->assertSee('Test Quiz');
        $response->assertSee('This is a test quiz.');
    }

    

    /** @test admin can see user results */
    public function test_admin_can_see_user_results()
    {
        $this->actingAs($this->user);
        $this->user->update(['role' => 'admin']);

        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@gmail.com',
        ]);
        $quiz = Quiz::factory()->create([
            'name' => 'Test Quiz',
            'description' => 'This is a test quiz.',
        ]);
        $question = Question::factory()->create([
            'quiz_id' => $quiz->id,
            'question' => 'What is your name?',
        ]);
        \App\Models\Result::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => 10,
            'time_taken' => 60, 
            'completed_at' => now(),
        ]);
        Answer::factory()->create([
            'question_id' => $question->id,
            'answer' => 'My name is John Doe.',
        ]);

        $response = $this->get('/admin/users/' . $user->id . '/results'); 
        $response->assertStatus(200);
        $response->assertSee('10');
    }
    
}