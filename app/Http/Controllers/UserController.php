<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepositRequest;
use App\Http\Requests\CreateUserRequest;
use App\Services\CreateDepositService;
use App\Services\CreateUserService;

class UserController extends Controller
{
    public function create(CreateUserRequest $request)
    {
        return (new CreateUserService())->execute($request->validated());
    }

    public function deposit(CreateDepositRequest $request)
    {
        return (new CreateDepositService())->execute(
            (string) auth()->id(),
            (string) $request->validated('value')
        );
    }
}
