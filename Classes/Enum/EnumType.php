<?php

declare(strict_types=1);

namespace GeorgRinger\PinnedContent\Enum;

enum EnumType: int
{
    case New = 0;
    case Template = 1;
    case Pinned = 2;
}
