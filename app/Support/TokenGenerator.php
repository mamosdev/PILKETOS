<?php

namespace App\Support;

use Illuminate\Support\Facades\Hash;

class TokenGenerator
{
    public static function make(int $length = 6): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $max = strlen($alphabet) - 1;
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $alphabet[random_int(0, $max)];
        }

        return $token;
    }

    public static function hash(string $token): string
    {
        return Hash::make($token, ['rounds' => 8]);
    }
}