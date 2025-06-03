<?php

declare(strict_types=1);

namespace GeorgRinger\FavoriteContent\Enum;

enum EnumType: int
{
    case New = 0;
    case Copy = 1;
    case Favorite = 2;
}
