<?php
namespace App\Enums;

enum DietaryPreferencesEnum: string
{
    case VEGETARIAN = 'vegetarian';
    case VEGAN = 'vegan';
    case PESCATARIAN = 'pescatarian';
    case PALEO = 'paleo';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}