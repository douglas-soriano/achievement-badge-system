<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAchievementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Extracting data from the user achievements
        $unlocked_achievements = $this['unlocked_achievements'];
        $next_available_achievements = $this['next_available_achievements'];
        $current_badge = $this['current_badge'];
        $next_badge = $this['next_badge'];
        $remaining_to_unlock_next_badge = $this['remaining_to_unlock_next_badge'];

        // Creating an array representation of the user achievements
        return [
            'unlocked_achievements' => $unlocked_achievements ? $unlocked_achievements->toArray() : [],
            'next_available_achievements' => $next_available_achievements ? $next_available_achievements->toArray() : [],
            'current_badge' => $current_badge,
            'next_badge' => $next_badge,
            'remaining_to_unlock_next_badge' => $remaining_to_unlock_next_badge,
        ];
    }
}
