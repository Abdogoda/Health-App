<?php

namespace App\Enums;

enum MedicalConditionsEnum: string
{
    case DIABETES = 'diabetes';
    case HYPERTENSION = 'hypertension';
    case HEART_DISEASE = 'heart_disease';
    case ASTHMA = 'asthma';
    case KIDNEY_DISEASE = 'kidney_disease';
    case LIVER_DISEASE = 'liver_disease';
    case ANEMIA = 'anemia';
    case OBESITY = 'obesity';
    case THYROID_DISORDER = 'thyroid_disorder';
    case HEPATITIS_C = 'hepatitis_c';
    case CANCER = 'cancer';
    case OSTEOPOROSIS = 'osteoporosis';
    case STROKE = 'stroke';
    case GASTRIC_ULCER = 'gastric_ulcer';
    case MIGRAINE = 'migraine';
    case DEPRESSION = 'depression';
    case EPILEPSY = 'epilepsy';
    case AUTOIMMUNE_DISEASE = 'autoimmune_disease';
}
