<?php

namespace App\Http\Controllers;

use App\Http\Requests\{CreateUserRequest, CreateDepositRequest};
use App\Services\CreateDepositService;
use App\Services\CreateUserService;
use Illuminate\Http\Request;

class UserController extends Controller {
    public function create(CreateUserRequest $request){
        $createUserService = new CreateUserService();

        return $createUserService->execute($request->all());
    }

    public function deposit(CreateDepositRequest $request){
        $createDepositService = new CreateDepositService();

        return $createDepositService->execute(
            auth()->user()->id,
            $request->value
        );
    }
}