<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'birth_date',
        'height',
        'weight',
        'gender',
        'health_goal',
        'activity_level',
        'dietary_preference',
        'dietary_instructions',
        'daily_caloric_target',
        'protein_target',
        'carbs_target',
        'fat_target',
        'receive_daily_report',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'receive_daily_report' => 'boolean',
        'dietary_instructions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->second_name} {$this->last_name}";
    }
}
