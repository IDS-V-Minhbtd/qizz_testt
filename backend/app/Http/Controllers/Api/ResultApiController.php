<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\ResultService;
use App\Http\Resources\ResultResource;
use App\Http\Controllers\Controller;

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
        return ResultResource::collection($results);
    }

    public function show($id)
    {
        $result = $this->resultService->findById($id);
        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Kết quả không tồn tại!'
            ], 404);
        }
        return new ResultResource($result);
    }

    public function destroy($id)
    {
        $deleted = $this->resultService->deleteById($id);
        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Kết quả đã được xóa thành công!'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Xóa thất bại!'
        ], 400);
    }
}