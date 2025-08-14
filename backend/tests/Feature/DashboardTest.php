<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Answer;
use App\Models\Question;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    
    /**@test giao diện dashboard của user*/
    public function test_user_dashboard()
    {
        $this->actingAs($this->user);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }
    /**@test giao diện dashboard của nonuser*/
    public function test_nonuser_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
    /**@test giao diện dashboard của user*/
    public function user_can_see_quiz_list()
    {
        $this->actingAs($this->user);
        $quiz = Quiz::factory()->create();
        $response = $this->get('/dashboard');
        $response->assertSee($quiz->title);
    }
    

}
