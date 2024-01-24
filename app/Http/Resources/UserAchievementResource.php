<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAchievementResource extends JsonResource
{
    // Remove 'data' wrapper.
    public static $wrap = false;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Creating an array representation of the user achievements
        return [
            'unlocked_achievements' => $this->unlocked_achievements ? $this->unlocked_achievements->toArray() : [],
            'next_available_achievements' => $this->next_available_achievements ? $this->next_available_achievements->toArray() : [],
            'current_badge' => $this->current_badge,
            'next_badge' => $this->next_badge,
            'remaining_to_unlock_next_badge' => $this->remaining_to_unlock_next_badge,
        ];
    }
}
