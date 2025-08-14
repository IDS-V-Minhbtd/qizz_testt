<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'quizz_code' => $this->quizz_code,
            'time_limit' => $this->time_limit,
            'is_public' => (bool) $this->is_public,
            'created_by' => $this->created_by,
            'code' => $this->code,
            'catalog_id' => $this->catalog_id,
            'lesson_id' => $this->lesson_id,
            'popular' => (bool) $this->popular,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Relationships
            'catalog' => $this->whenLoaded('catalog', function () {
                return [
                    'id' => $this->catalog->id,
                    'name' => $this->catalog->name,
                ];
            }),
            
         
            
            
        ];
    }
}
