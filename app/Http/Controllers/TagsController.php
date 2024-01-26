<?php

namespace App\Http\Controllers;

use App\Services\UserService;

class TagsController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $items = $this->userService->getTags(auth()->id());
        $result = [
            'status' => 'success', 'message' => 'タグを取得一覧を取得しました。', 'count' => count($items), 'items' => $items];
        return response()->json($result, 200);
    }

}
