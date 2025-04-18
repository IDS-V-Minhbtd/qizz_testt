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
        $this->user->assignRole('admin');
        $response = $this->get('/admin/quizzes'); // Sửa lại đường dẫn

        $response->assertStatus(200);
    }

    /** @test admin can create quiz */
    public function test_admin_can_create_quiz()
    {
        $this->actingAs($this->user);
        $this->user->assignRole('admin');

        $response = $this->post('/admin/quizzes', [ // Sửa lại đường dẫn
            'title' => 'Test Quiz',
            'description' => 'This is a test quiz.',
            'user_id' => $this->user->id,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/quizzes');
        $this->assertDatabaseHas('quizzes', [
            'title' => 'Test Quiz',
            'description' => 'This is a test quiz.',
        ]);
    }

    /** @test admin can edit quiz */
    public function test_admin_can_edit_quiz()
    {
        $this->actingAs($this->user);
        $this->user->assignRole('admin');

        $quiz = Quiz::factory()->create([
            'title' => 'Test Quiz',
            'description' => 'This is a test quiz.',
            'user_id' => $this->user->id,
        ]);

        $response = $this->put('/admin/quizzes/' . $quiz->id, [ // Sửa lại đường dẫn
            'title' => 'Updated Quiz',
            'description' => 'This is an updated test quiz.',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/quizzes');
        $this->assertDatabaseHas('quizzes', [
            'title' => 'Updated Quiz',
            'description' => 'This is an updated test quiz.',
        ]);
    }

    /** @test admin can delete quiz */
    public function test_admin_can_delete_quiz()
    {
        $this->actingAs($this->user);
        $this->user->assignRole('admin');

        $quiz = Quiz::factory()->create([
            'title' => 'Test Quiz',
            'description' => 'This is a test quiz.',
            'user_id' => $this->user->id,
        ]);

        $response = $this->delete('/admin/quizzes/' . $quiz->id); // Sửa lại đường dẫn
        $response->assertStatus(302);
        $response->assertRedirect('/admin/quizzes');
        $this->assertDatabaseMissing('quizzes', [
            'title' => 'Test Quiz',
            'description' => 'This is a test quiz.',
        ]);
    }

    /** @test admin can see quiz */
    public function test_admin_can_see_quiz()
    {
        $this->actingAs($this->user);
        $this->user->assignRole('admin');

        $quiz = Quiz::factory()->create([
            'title' => 'Test Quiz',
            'description' => 'This is a test quiz.',
            'user_id' => $this->user->id,
        ]);

        $response = $this->get('/admin/quizzes/' . $quiz->id); // Sửa lại đường dẫn
        $response->assertStatus(200);
        $response->assertSee('Test Quiz');
        $response->assertSee('This is a test quiz.');
    }

    /** @test non-admin user cannot access admin page */
    public function test_non_admin_user_cannot_access_admin_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/admin/quizzes'); // Sửa lại đường dẫn
        $response->assertStatus(403);
    }

    /** @test admin can see question */
    public function test_admin_can_see_question()
    {
        $this->actingAs($this->user);
        $this->user->assignRole('admin');

        $quiz = Quiz::factory()->create([
            'title' => 'Test Quiz',
            'description' => 'This is a test quiz.',
            'user_id' => $this->user->id,
        ]);

        $question = Question::factory()->create([
            'quiz_id' => $quiz->id,
            'question' => 'What is your name?',
        ]);

        $response = $this->get('/admin/quizzes/' . $quiz->id . '/questions/' . $question->id); // Sửa lại đường dẫn
        $response->assertStatus(200);
        $response->assertSee('What is your name?');
    }

    /** @test non-admin user cannot see quiz CRUD */
    public function test_non_admin_user_cannot_see_quiz_crud()
    {
        $this->actingAs($this->user);

        $response = $this->get('/admin/quizzes/create'); // Sửa lại đường dẫn
        $response->assertStatus(403);

        $response = $this->get('/admin/quizzes/' . $this->user->id . '/edit'); // Sửa lại đường dẫn
        $response->assertStatus(403);

        $response = $this->get('/admin/quizzes/' . $this->user->id . '/delete'); // Sửa lại đường dẫn
        $response->assertStatus(403);
    }

    /** @test admin can see admin dashboard */
    public function test_admin_can_see_admin_dashboard()
    {
        $this->actingAs($this->user);
        $this->user->assignRole('admin');

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    /** @test admin can see user results */
    public function test_admin_can_see_user_results()
    {
        $this->actingAs($this->user);
        $this->user->assignRole('admin');

        $quiz = Quiz::factory()->create([
            'title' => 'Test Quiz',
            'description' => 'This is a test quiz.',
            'user_id' => $this->user->id,
        ]);

        $question = Question::factory()->create([
            'quiz_id' => $quiz->id,
            'question' => 'What is your name?',
        ]);

        Answer::factory()->create([
            'question_id' => $question->id,
            'answer' => 'My name is John Doe.',
        ]);

        $response = $this->get('/admin/quizzes/' . $quiz->id . '/results'); // Sửa lại đường dẫn
        $response->assertStatus(200);
        $response->assertSee('My name is John Doe.');
    }
}
