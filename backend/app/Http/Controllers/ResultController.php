<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ResultService;

class ResultController extends Controller
{
    protected $resultService;

    public function __construct(ResultService $resultService)
    {
        $this->resultService = $resultService;
    }
    public function index()
    {
        $results = $this->resultService->getAll(); 
        return view('admin.results.index', compact('results'));
    }
    public function show($id)
    {
        $result = $this->resultService->findById($id);
        if (!$result) {
            return redirect()->route('admin.results.index')->with('error', 'Kết quả không tồn tại!');
        }
        return view('admin.results.show', compact('result'));
    }

    public function destroy($id)
    {
        $deleted = $this->resultService->deleteById($id);
        if ($deleted) {
            return redirect()->route('admin.results.index')->with('success', 'Kết quả đã được xóa thành công!');
        }
        return redirect()->route('admin.results.index')->with('error', 'Xóa thất bại!');
    }
}
