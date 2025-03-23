<?php

namespace App\Enums;

enum DishesEnum: string
{
    case MAIN_COURSE = 'main course';
    case SIDE_DISH = 'side dish';
    case DESSERT = 'dessert';
    case APPETIZER = 'appetizer';
    case SALAD = 'salad';
    case BREAD = 'bread';
    case BREAKFAST = 'breakfast';
    case SOUP = 'soup';
    case BEVERAGE = 'beverage';
    case SAUCE = 'sauce';
    case MARINADE = 'marinade';
    case FINGERFOOD = 'fingerfood';
    case SNACK = 'snack';
    case DRINK = 'drink';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
