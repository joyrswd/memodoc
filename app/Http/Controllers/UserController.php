<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function entry()
    {
        return view('user.entry');
    }

    public function register(UserRequest $request)
    {
        $this->userService->register([
            'name' => $request->input('user_name'),
            'email' => $request->input('user_email'),
            'password' => $request->input('user_password'),
        ]);
        return redirect()->route('memo.create');
    }

}
