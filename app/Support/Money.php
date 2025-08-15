<?php

namespace App\Support;

final class Money
{
    public static function mul(int $cents, int $num, int $den = 1): int
    {
        // integer-safe multiply/divide, round half up
        $value = ($cents * $num) / $den;

        return (int) floor($value + 0.5);
    }
}
