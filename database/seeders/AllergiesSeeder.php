<?php

namespace Database\Seeders;

use App\Enums\AllergiesEnum;
use App\Models\Allergy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AllergiesSeeder extends Seeder
{
    public function run(): void
    {
        $allergies = collect(AllergiesEnum::cases())->map(fn($case) => [
            'name' => $case->value
        ])->toArray();

        Allergy::insertOrIgnore($allergies);
    }
}
