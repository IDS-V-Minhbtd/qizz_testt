<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Tạo admin user để login
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    /** @test admin xem form tạo câu hỏi */
    public function test_admin_can_view_create_question_form()
    {
        $quiz = Quiz::factory()->create();
        $response = $this->get(route('admin.quizzes.questions.create', $quiz->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.quizzes.question.create');
    }

    /** @test admin tạo mới câu hỏi multiple_choice */
    public function test_admin_can_store_multiple_choice_question()
    {
        $quiz = Quiz::factory()->create();

        $response = $this->post(route('admin.quizzes.questions.store', $quiz->id), [
            'question' => 'Câu hỏi trắc nghiệm',
            'order' => 1,
            'answer_type' => 'multiple_choice',
            'answers' => [
                1 => ['text' => 'Đáp án 1'],
                2 => ['text' => 'Đáp án 2'],
            ],
            'correct_answer' => '1',
        ]);

        $response->assertRedirect(route('admin.quizzes.edit', $quiz->id));
        $this->assertDatabaseHas('questions', [
            'quiz_id' => $quiz->id,
            'question' => 'Câu hỏi trắc nghiệm'
        ]);
    }

    /** @test admin xem form sửa câu hỏi */
    public function test_admin_can_view_edit_question_form()
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);

        $response = $this->get(route('admin.quizzes.questions.edit', [$quiz->id, $question->id]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.quizzes.question.edit');
    }

    /** @test admin chỉnh sửa câu hỏi multiple_choice */
    public function test_admin_can_update_multiple_choice_question()
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create([
            'quiz_id' => $quiz->id,
            'order' => 1,
            'question' => 'Câu hỏi cũ',
            'type' => 'multiple_choice',
        ]);

        // Tạo sẵn 2 đáp án cho câu hỏi
        $question->answers()->createMany([
            ['answer' => 'Đáp án cũ 1', 'is_correct' => true],
            ['answer' => 'Đáp án cũ 2', 'is_correct' => false],
        ]);

        $response = $this->put(route('admin.quizzes.questions.update', [$quiz->id, $question->id]), [
            'question' => 'Câu hỏi đã cập nhật',
            'order' => 2,
            'answer_type' => 'multiple_choice',
            'answers' => [
                0 => ['text' => 'Đáp án mới 1'],
                1 => ['text' => 'Đáp án mới 2'],
            ],
            'correct_answer' => '2',
        ]);

        $response->assertRedirect(route('admin.quizzes.edit', $quiz->id));
        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'question' => 'Câu hỏi đã cập nhật',
            'order' => 2,
        ]);
        $this->assertDatabaseHas('answers', [
            'question_id' => $question->id,
            'answer' => 'Đáp án mới 2',
            'is_correct' => 1,
        ]);
    }

    /** @test admin xóa câu hỏi */
    public function test_admin_can_delete_question()
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);

        $response = $this->delete(route('admin.quizzes.questions.destroy', [$quiz->id, $question->id]));

        $response->assertRedirect(route('admin.quizzes.edit', $quiz->id));
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    }
}
