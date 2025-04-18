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

    /**@test admin quizz*/
    public function test_admin_quizz()
    {
        $this->actingAs($this->user);
        $this->user->assignRole('admin');
        $response = $this->get('/admin/quizz');

        $response->assertStatus(200);
        
    }
    /**@test admin quizz create*/
    public function test_admin_quizz_create()
    {
        $this->actingAs($this->user);
        $this->user->assignRole('admin');
        $this->factory(Quiz::class)->create(
            [
                'title' => 'Test Quiz',
                'description' => 'This is a test quiz.',
                'user_id' => $this->user->id,
            ]
        );
        $response->assertStatus(200);
        $this->assertDatabaseHas('quizzes', [
            'title' => 'Test Quiz',
            'description' => 'This is a test quiz.',
        ]);

    }
    /**@test admin quizz edit*/
    public function test_admin_quizz_edit()
    {
        $this->actingAs($this->user);
        $this->user->assignRole('admin');
        $quiz = Quiz::factory()->create(
            [
                'title' => 'Test Quiz',
                'description' => 'This is a test quiz.',
                'user_id' => $this->user->id,
            ]
        );
        $this->put('/admin/quizz/edit' . $quiz->id, [
            'title' => 'Updated Quiz',
            'description' => 'This is an updated test quiz.',
        ]);
        $response = $this->get('/admin/quizz/' . $quiz->id);
        $response->assertStatus(302);
        $response->assertRedirect('/admin/quizz');
        $this->assertDatabaseHas('quizzes', [
            'title' => 'Updated Quiz',
            'description' => 'This is an updated test quiz.',
        ]);
       
        
    }
    /**@test admin quizz delete*/
    public function test_admin_quizz_delete()
    {
        $this->actingAs($this->user);
        $this->user->assignRole('admin');
        $quiz = Quiz::factory()->create(
            [
                'title' => 'Test Quiz',
                'description' => 'This is a test quiz.',
                'user_id' => $this->user->id,
            ]
        );
        $response = $this->delete('/admin/quizz/delete' . $quiz->id);
        $response->assertStatus(302);
        $response->assertRedirect('/admin/quizz');
        $this->assertDatabaseMissing('quizzes', [
            'title' => 'Test Quiz',
            'description' => 'This is a test quiz.',
        ]);
        
    }
    /**@test admin can see quizz */
    public function test_admin_can_see_quizz()
    {
        $this->actingAs($this->user);
        $this->user->assignRole('admin');
        $quiz = Quiz::factory()->create(
            [
                'title' => 'Test Quiz',
                'description' => 'This is a test quiz.',
                'user_id' => $this->user->id,
            ]
        );
        $response = $this->get('/admin/quizz/' . $quiz->id);
        $response->assertStatus(200);
        $response->assertSee('Test Quiz');
        $response->assertSee('This is a test quiz.');
        
    }
    /**@test non admin user cant access admin page */
    public function test_non_admin_user_cant_access_admin_page()
    {
        $this->actingAs($this->user);
        $response = $this->get('/admin/quizz');
        $response->assertStatus(403);
        
    }
    /**@test admin can see question */
    public function test_admin_can_see_question()
    {
        $this->actingAs($this->user);
        $this->user->assignRole('admin');
        $quiz = Quiz::factory()->create(
            [
                'title' => 'Test Quiz',
                'description' => 'This is a test quiz.',
                'user_id' => $this->user->id,
            ]
        );
        $question = Question::factory()->create(
            [
                'quiz_id' => $quiz->id,
                'question' => 'What is your name?',
            ]
        );
        $response = $this->get('/admin/quizz/' . $quiz->id . '/questions/' . $question->id);
        $response->assertStatus(200);
        $response->assertSee('What is your name?');
        
    }

}
