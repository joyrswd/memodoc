<?php

namespace App\Http\Controllers;

use App\Services\PartsService;
use Illuminate\Http\Request;

class PartsController extends Controller
{
    private PartsService $partsService;

    public function __construct(PartsService $partsService)
    {
        $this->partsService = $partsService;
    }

    public function index()
    {
        return view('parts.index', [
            'items' => $this->partsService->getParts(auth()->id()),
        ]);
    }

    public function add(int $id)
    {
        $result = $this->partsService->addParts($id);
        $status = ($result['status'] === PartsService::STATUS_SUCCESS) ? 200 : 422;
        return response()->json($result, $status);
    }

    public function remove(int $id = null)
    {
        $result = $this->partsService->deleteParts($id);
        $status = ($result['status'] === PartsService::STATUS_SUCCESS) ? 200 : 422;
        return response()->json($result, $status);
    }

    public function update(Request $request)
    {
        $result = $this->partsService->updateParts($request->input('memo', []));
        $status = ($result['status'] === PartsService::STATUS_SUCCESS) ? 200 : 422;
        return response()->json($result, $status);
    }
}
