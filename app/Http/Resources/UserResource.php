<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'second_name' => $this->second_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'picture_url' => $this->picture_url,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'joined_at' => $this->created_at,

            'profile' => new UserProfileResource($this->profile),
            'dietary_preferences' => $this->whenLoaded('dietaryPreferences', function () {
                return $this->dietaryPreferences->pluck('name');
            }),

        ];
    }
}
