<?php

declare(strict_types=1);

namespace GeorgRinger\FavoriteContent\Enum;

enum EnumType: int
{
    case New = 1;
    case Copy = 2;
    case Favorite = 3;
}
