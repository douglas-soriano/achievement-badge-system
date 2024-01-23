<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AchievementService;
use App\Http\Resources\UserAchievementResource;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    private AchievementService $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function index(User $user)
    {
        // Delegate logic to AchievementService
        $achievementData = $this->achievementService->getUserAchievements($user);

        // Return JSON response with achievement data
        return new UserAchievementResource($achievementData);
    }
}
