<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Result;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
 /** @test user can see list of public quizzes */
public function test_user_can_see_public_quizzes()
{
    $this->actingAs($this->user);
    $this->user->assignRole('user');

    Quiz::factory()->create([
        'title' => 'Public Quiz',
        'description' => 'This is a public quiz.',
        'is_public' => true,
        'created_by' => $this->user->id,
    ]);

    $response = $this->get('/quizzes');
    $response->assertStatus(200);
    $response->assertSee('Public Quiz');
}
/** @test user can view quiz with questions */
public function test_user_can_view_quiz_with_questions()
{
    $this->actingAs($this->user);
    $this->user->assignRole('user');

    $quiz = Quiz::factory()->create([
        'title' => 'Quiz with Questions',
        'description' => 'This quiz has questions.',
        'is_public' => true,
        'created_by' => $this->user->id,
    ]);

    Question::factory()->create([
        'quiz_id' => $quiz->id,
        'question_text' => 'What is the capital of France?',
    ]);

    $response = $this->get('/quizzes/' . $quiz->id);
    $response->assertStatus(200);
    $response->assertSee('What is the capital of France?');

}
/** @test user can submit quiz and see result */
public function test_user_can_submit_quiz_and_receive_result()
{
    $this->actingAs($this->user);
    $this->user->assignRole('user');

    $quiz = Quiz::factory()->create([
        'title' => 'Quiz to Submit',
        'description' => 'This quiz can be submitted.',
        'is_public' => true,
        'created_by' => $this->user->id,
    ]);

    Question::factory()->create([
        'quiz_id' => $quiz->id,
        'question_text' => 'What is the capital of France?',
    ]);

    Answer::factory()->create([
        'question_id' => 1,
        'answer_text' => 'Paris',
        'is_correct' => true,
    ]);

    $response = $this->post('/quizzes/' . $quiz->id . '/submit', [
        'answers' => [
            1 => 1, // Assuming the answer ID is 1
        ],
    ]);

    $response->assertStatus(200);
    $response->assertSee('Your result:');

}
/** @test user cannot access private or non-existent quiz */
public function test_user_cannot_access_private_or_nonexistent_quiz()
{
    $this->actingAs($this->user);
    $this->user->assignRole('user');

    $quiz = Quiz::factory()->create([
        'title' => 'Private Quiz',
        'description' => 'This quiz is private.',
        'is_public' => false,
        'created_by' => $this->user->id,
    ]);

    $response = $this->get('/quizzes/' . $quiz->id);
    $response->assertStatus(403);

    $response = $this->get('/quizzes/9999'); // Non-existent quiz
    $response->assertStatus(404);


}
/** @test dashboard shows user quiz results */
public function test_dashboard_shows_user_quiz_results()
{
    $this->actingAs($this->user);
    $this->user->assignRole('user');

    $quiz = Quiz::factory()->create([
        'title' => 'Quiz for Dashboard',
        'description' => 'This quiz is for dashboard testing.',
        'is_public' => true,
        'created_by' => $this->user->id,
    ]);

    Result::factory()->create([
        'quiz_id' => $quiz->id,
        'user_id' => $this->user->id,
        'score' => 80,
    ]);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
    $response->assertSee('Quiz for Dashboard');
}
/** @test user when they hasn't chose quizz */
public function test_user_when_they_hasnt_chose_quizz()
{
    $this->actingAs($this->user);
    $this->user->assignRole('user');

    $response = $this->get('/quizzes');
    $response->assertStatus(200);
    $response->assertSee('hãy hoàn thành một bài kiểm tra');
}
/** @test user cannot view another user's quiz results */
public function test_user_cannot_view_another_users_quiz_results()
{
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    $quiz = Quiz::factory()->create(['is_public' => true]);
    $question = Question::factory()->create(['quiz_id' => $quiz->id]);
    $answer = Answer::factory()->create(['question_id' => $question->id, 'is_correct' => true]);

    // User1 trả lời quiz
    $this->actingAs($user1);
    $this->postJson('/api/quizzes/' . $quiz->id . '/submit', [
        'answers' => [
            $question->id => $answer->answer
        ]
    ]);

    // User2 cố xem kết quả của User1
    $this->actingAs($user2);
    $response = $this->getJson('/api/results');
    $response->assertStatus(403); // Không thể xem kết quả của người khác
}
/** @test user cannot submit quiz after time limit */
public function test_user_cannot_submit_after_time_limit()
{
    // Tạo quiz với time_limit
    $quiz = Quiz::factory()->create(['is_public' => true, 'time_limit' => 60]);
    $question = Question::factory()->create(['quiz_id' => $quiz->id]);
    $answer = Answer::factory()->create(['question_id' => $question->id, 'is_correct' => true]);

    // Giả lập thời gian đã hết
    Carbon::setTestNow(now()->addMinutes(61));

    // Gửi câu trả lời khi đã quá thời gian
    $response = $this->postJson('/api/quizzes/' . $quiz->id . '/submit', [
        'answers' => [
            $question->id => $answer->answer
        ]
    ]);

    $response->assertStatus(400); // Lỗi do hết thời gian
    $response->assertJson(['error' => 'Time limit exceeded']);
}





}