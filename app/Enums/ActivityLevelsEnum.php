<?php

namespace App\Enums;

enum ActivityLevelsEnum: string
{
    case SEDENTARY = '1.2';
    case LIGHTLY_ACTIVE = '1.375';
    case MODERATELY_ACTIVE = '1.55';
    case VERY_ACTIVE = '1.725';
    case SUPER_ACTIVE = '1.9';

    public static function getOptions(): array
    {
        return [
            self::SEDENTARY->value => 'Sedentary (little to no exercise)',
            self::LIGHTLY_ACTIVE->value => 'Lightly Active (1-3 days/week)',
            self::MODERATELY_ACTIVE->value => 'Moderately Active (3-5 days/week)',
            self::VERY_ACTIVE->value => 'Very Active (6-7 days/week)',
            self::SUPER_ACTIVE->value => 'Super Active (hard exercise/physical job)',
        ];
    }
}
