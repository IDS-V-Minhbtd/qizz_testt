<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProgressService;
use App\Http\Resources\ProgressResource;

class ProgressController extends Controller
{
    protected $progressService;

    public function __construct(ProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Lấy tất cả progress của user hiện tại
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $progressList = $this->progressService->getByUser($userId);

        return ProgressResource::collection($progressList);
    }

    /**
     * Lấy tiến độ theo khóa học
     */
    public function getCourseProgress($courseId, Request $request)
    {
        $userId = $request->user()->id;
        $progress = $this->progressService->getByCourse($userId, $courseId);

        return ProgressResource::collection($progress);
    }



    /**
     * Reset tiến độ khóa học
     */
    public function resetCourseProgress($courseId, Request $request)
    {
        $userId = $request->user()->id;

        $this->progressService->resetCourseProgress($userId, $courseId);

        return response()->json([
            'success' => true,
            'message' => 'Progress reset successfully'
        ]);
    }
}
