<?php

namespace App\Services;

use App\Exceptions\AppError;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Facades\DB;

class CreateDepositService
{
    public function execute(string $userId, string $value): User
    {
        $amountCents = Money::toCents($value);

        return DB::transaction(function () use ($userId, $amountCents) {
            $user = User::query()->whereKey($userId)->lockForUpdate()->first();

            if (!$user) {
                throw new AppError('Usuário não encontrado.', 404);
            }

            $newBalance = Money::toCents((string) $user->balance) + $amountCents;

            if ($newBalance > Money::MAX_CENTS) {
                throw new AppError('O depósito excede o limite de saldo suportado.', 422);
            }

            $user->balance = Money::fromCents($newBalance);
            $user->save();

            return $user;
        }, 3);
    }
}
