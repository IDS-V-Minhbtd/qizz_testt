<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'quiz_id' => $this->quiz_id,
            'score' => $this->score,
            'time_taken' => $this->time_taken,
            'completed_at' => $this->completed_at->toDateTimeString(),
            'quiz' => $this->whenLoaded('quiz', fn() => [
                'id' => $this->quiz->id,
                'name' => $this->quiz->name,
                'description' => $this->quiz->description,
            ]),
            'user_answers' => UserAnswerResource::collection($this->whenLoaded('userAnswers')),
        ];
    }
}