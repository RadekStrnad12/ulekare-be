<?php

namespace App\Enum;

enum Color: string
{
    case RED = 'red';
    case AMBER = 'amber';
    case EMERALD = 'emerald';
    case SKY = 'sky';
    case PURPLE = 'purple';
    case SLATE = 'slate';

    public function label(): string
    {
        return match($this) {
            self::RED => 'Red',
            self::AMBER => 'Amber',
            self::EMERALD => 'Emerald',
            self::SKY => 'Sky',
            self::PURPLE => 'Purple',
            self::SLATE => 'Slate',
        };
    }
}
