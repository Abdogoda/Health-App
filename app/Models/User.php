<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'second_name',
        'last_name',
        'picture',
        'email',
        'password',
        'otp',
        'otp_expires_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->second_name} {$this->last_name}";
    }

    public function getPictureUrlAttribute(): string
    {
        return $this->picture ? asset("storage/{$this->picture}") : 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function uploadPicture($file): void
    {
        $imagePath = $file->store('images/users', 'public');

        $this->deletePicture();

        $this->update([
            'picture' => $imagePath,
        ]);
    }

    public function deletePicture(): void
    {
        if ($this->picture) {
            Storage::disk('public')->delete($this->picture);
        }
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function medicalConditions(): BelongsToMany
    {
        return $this->belongsToMany(MedicalCondition::class, 'medical_condition_user');
    }

    public function familyMedicalHistories(): BelongsToMany
    {
        return $this->belongsToMany(FamilyMedicalHistory::class, 'family_medical_history_user');
    }

    public function allergies(): BelongsToMany
    {
        return $this->belongsToMany(Allergy::class, 'allergy_user');
    }


    public function mealPlans(): HasMany
    {
        return $this->hasMany(MealPlan::class);
    }

    public function favoriteMealPlans(): BelongsToMany
    {
        return $this->belongsToMany(MealPlan::class, 'user_favorites');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function favoriteRecipes(): HasMany
    {
        return $this->recipes()->where('is_favorite', true);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }
}
