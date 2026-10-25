<?php

namespace App\Support;

use InvalidArgumentException;

final class Money
{
    // As migrations originais usam DECIMAL(8, 2): máximo de 999999.99.
    public const MAX_CENTS = 99_999_999;

    public static function toCents(string $amount): int
    {
        if (!preg_match('/^\d{1,6}(?:\.\d{1,2})?$/D', $amount)) {
            throw new InvalidArgumentException('Valor monetário inválido.');
        }

        [$reais, $centavos] = array_pad(explode('.', $amount, 2), 2, '');

        return ((int) $reais * 100) + (int) str_pad($centavos, 2, '0');
    }

    public static function fromCents(int $cents): string
    {
        if ($cents < 0 || $cents > self::MAX_CENTS) {
            throw new InvalidArgumentException('Valor fora do limite do banco.');
        }

        return sprintf('%d.%02d', intdiv($cents, 100), $cents % 100);
    }
}
