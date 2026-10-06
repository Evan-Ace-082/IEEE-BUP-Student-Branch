<?php

namespace App\Support;

class HumanCheck
{
    public static function issue(): string
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        session([
            'human_check' => $a + $b,
            'human_prompt' => $a.' + '.$b,
        ]);

        return (string) session('human_prompt');
    }

    public static function prompt(): string
    {
        return (string) session('human_prompt', '');
    }

    public static function passes(?string $answer): bool
    {
        $expected = session('human_check');
        session()->forget('human_check');

        if ($expected === null || $answer === null || trim($answer) === '') {
            return false;
        }

        return (int) trim($answer) === (int) $expected;
    }
}
