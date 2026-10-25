<?php

namespace App\Services;

use App\Exceptions\AppError;
use App\Models\Transaction;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Facades\DB;

class CreateTransactionService
{
    public function execute(array $data): Transaction
    {
        // Não confiar no pagador enviado pelo cliente: deve ser quem fez login.
        if ((string) $data['payer'] !== (string) auth()->id()) {
            throw new AppError('Você não pode transferir da conta de outro usuário.', 403);
        }

        if ($data['payer'] === $data['payee']) {
            throw new AppError('Não é possível transferir para a própria conta.', 422);
        }

        $amountCents = Money::toCents((string) $data['value']);

        return DB::transaction(function () use ($data, $amountCents) {
            // Ordem fixa de bloqueio para reduzir o risco de deadlocks.
            $users = User::query()
                ->whereIn('id', [$data['payer'], $data['payee']])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $payer = $users->get($data['payer']);
            $payee = $users->get($data['payee']);

            if (!$payer || !$payee) {
                throw new AppError('Pagador ou destinatário não encontrado.', 404);
            }

            if ($payer->type === 'SELLER') {
                throw new AppError('Lojistas não podem enviar transferências.', 403);
            }

            $payerCents = Money::toCents((string) $payer->balance);
            $payeeCents = Money::toCents((string) $payee->balance);

            if ($payerCents < $amountCents) {
                throw new AppError('Saldo insuficiente para a transação.', 400);
            }

            if ($payeeCents + $amountCents > Money::MAX_CENTS) {
                throw new AppError('Saldo do destinatário excederia o limite suportado.', 422);
            }

            $payer->balance = Money::fromCents($payerCents - $amountCents);
            $payee->balance = Money::fromCents($payeeCents + $amountCents);

            $payer->save();
            $payee->save();

            return Transaction::create([
                'payer_id' => $payer->id,
                'payee_id' => $payee->id,
                'value' => Money::fromCents($amountCents),
            ]);
        }, 3);
    }
}
