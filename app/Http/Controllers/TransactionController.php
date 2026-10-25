<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransactionRequest;
use App\Services\CreateTransactionService;

class TransactionController extends Controller
{
    public function create(CreateTransactionRequest $request)
    {
        return (new CreateTransactionService())->execute($request->validated());
    }
}
