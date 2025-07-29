<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Services\ResultService;

class ResultApiController extends Controller
{
    protected $resultService;

    public function __construct(ResultService $resultService)
    {
        $this->resultService = $resultService;
    }

    public function index()
    {
        $results = $this->resultService->getAll();
        return response()->json($results);
    }

    public function show($id)
    {
        $result = $this->resultService->findByIdWithAnswers($id); // Fetch result with user answers
        if (!$result) {
            return response()->json(['message' => 'Result not found'], 404);
        }

        return response()->json([
            'result' => $result,
            'user_answers' => $result->userAnswers, // Include user answers
        ]);
    }
}
