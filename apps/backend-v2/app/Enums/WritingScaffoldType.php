<?php

declare(strict_types=1);

namespace App\Enums;

enum WritingScaffoldType: string
{
    case Template = 'template';
    case Guided = 'guided';
    case Freeform = 'freeform';
}
