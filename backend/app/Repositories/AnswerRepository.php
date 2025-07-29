<?php

namespace App\Repositories;

use App\Models\Answer;
use App\Models\Question;
use App\Repositories\Interfaces\AnswerRepositoryInterface;
use Illuminate\Support\Facades\Log;

class AnswerRepository implements AnswerRepositoryInterface
{
    public function create(array $data): Answer
    {
        Log::info('Dữ liệu lưu vào bảng answers:', $data);
        $answer = Answer::create($data);
        Log::info('Đáp án đã được lưu:', ['id' => $answer->id, 'answer' => $answer->answer]);
        return $answer;
    }

    public function findById(int $id): ?Answer
    {
        return Answer::find($id);
    }

    public function updateOrderByQuestionId(int $id, array $data): bool
    {
        $answer = $this->findById($id);
        if ($answer) {
            return $answer->update($data);
        }
        return false;
    }

    public function all(): iterable
    {
        return Answer::all();
    }

    public function getByQuestionId($questionId)
    {
        return Answer::where('question_id', $questionId)->get();
    }

    public function deleteByQuestionId(int $questionId): bool
    {
        return Answer::where('question_id', $questionId)->delete();
    }

    public function deleteById(int $id): bool
    {
        $answer = $this->findById($id);
        if ($answer) {
            return $answer->delete();
        }
        return false;
    }

    public function getAnswerByQuestionId(int $questionId, int $answerId): ?Answer
    {
        return Answer::where('id', $answerId)->where('question_id', $questionId)->first();
    }

    public function getCorrectAnswerAndAnswer($answer)
    {
        return Answer::where('question_id', $answer->question_id)
            ->where('is_correct', true)
            ->orWhere('id', $answer->id)
            ->get();
    }
}