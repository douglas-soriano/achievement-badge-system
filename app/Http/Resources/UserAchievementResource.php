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
        return [
            'unlocked_achievements' => $this->unlocked_achievements,
            'next_available_achievements' => $this->next_available_achievements,
            'current_badge' => $this->current_badge,
            'next_badge' => $this->next_badge,
            'remaining_to_unlock_next_badge' => $this->remaining_to_unlock_next_badge,
        ];
    }
}
