<?php

namespace App\Enums;

enum CuisinesEnum: string
{
    case AFRICAN = 'african';
    case AMERICAN = 'american';
    case ASIAN = 'asian';
    case ARABIC = 'arabic';
    case EGYPTIAN = 'egyptian';
    case LEBANESE = 'lebanese';
    case MOROCCAN = 'moroccan';
    case SYRIAN = 'syrian';
    case LIBYAN = 'libyan';
    case SAUDI = 'saudi';
    case YEMENI = 'yemeni';
    case ALGERIAN = 'algerian';
    case TUNISIAN = 'tunisian';
    case IRAQI = 'iraqi';
    case JORDANIAN = 'jordanian';
    case PALESTINIAN = 'palestinian';
    case KUWAITI = 'kuwaiti';
    case QATARI = 'qatari';
    case BAHRAINI = 'bahraini';
    case OMANI = 'omani';
    case EMIRATI = 'emirati';
    case IRANIAN = 'iranian';
    case TURKISH = 'turkish';
    case BRITISH = 'british';
    case CAJUN = 'cajun';
    case CARIBBEAN = 'caribbean';
    case CHINESE = 'chinese';
    case EASTERN_EUROPEAN = 'eastern european';
    case EUROPEAN = 'european';
    case FRENCH = 'french';
    case GERMAN = 'german';
    case GREEK = 'greek';
    case INDIAN = 'indian';
    case IRISH = 'irish';
    case ITALIAN = 'italian';
    case JAPANESE = 'japanese';
    case JEWISH = 'jewish';
    case KOREAN = 'korean';
    case LATIN_AMERICAN = 'latin american';
    case MEDITERRANEAN = 'mediterranean';
    case MEXICAN = 'mexican';
    case MIDDLE_EASTERN = 'middle eastern';
    case NORDIC = 'nordic';
    case SOUTHERN = 'southern';
    case SPANISH = 'spanish';
    case THAI = 'thai';
    case VIETNAMESE = 'vietnamese';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
