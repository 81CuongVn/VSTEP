<?php

declare(strict_types=1);

namespace App\Enums;

enum Skill: string
{
    case Listening = 'listening';
    case Reading = 'reading';
    case Writing = 'writing';
    case Speaking = 'speaking';
}
