<?php

declare(strict_types=1);

namespace App\Enums;

enum KnowledgePointCategory: string
{
    case Grammar = 'grammar';
    case Vocabulary = 'vocabulary';
    case Strategy = 'strategy';
}
