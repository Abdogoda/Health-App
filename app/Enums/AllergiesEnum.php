<?php
namespace App\Enums;

enum AllergiesEnum: string
{
  case POLLEN = 'pollen';
  case DUST = 'dust';
  case ANIMAL_DANDER = 'animal_dander';
  case MOLD = 'mold';
  case PEANUTS = 'peanuts';
  case TREE_NUTS = 'tree_nuts';
  case SEAFOOD = 'seafood';
  case MILK = 'milk';
  case EGG = 'egg';
  case WHEAT = 'wheat';
  case SOY = 'soy';
  case SESAME = 'sesame';
  case BEE_STING = 'bee_sting';
  case MEDICATIONS = 'medications';
}
