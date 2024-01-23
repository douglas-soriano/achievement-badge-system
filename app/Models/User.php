<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\UserAchievement;
use App\Models\UserBadge;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\Relations\hasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The comments that belong to the user.
     */
    public function comments() : hasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons() : belongsToMany
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched() : belongsToMany
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    /**
     * The achievements unlocked by this user.
     */
    public function achievements() : hasManyThrough
    {
        return $this->hasManyThrough(Achievement::class, UserAchievement::class, 'user_id', 'id', 'id', 'achievement_id');
    }

    /**
     * The badges unlocked by this user.
     */
    public function badges() : hasManyThrough
    {
        return $this->hasManyThrough(Badge::class, UserBadge::class, 'user_id', 'id', 'id', 'badge_id');
    }

    /**
     * Check if the user has this achievement
     **/
    public function hasAchievement(Achievement $achievement, bool $disable_cache = false): bool
    {
        if ($disable_cache)
            $this->load('achievements');
        return $this->achievements && $this->achievements->contains($achievement);
    }

    /**
     * Unlock achievement for user and fire event.
     */
    public function unlockAchievement(Achievement $achievement) : void
    {
        // Check if the user already has the achievement
        if (!$this->hasAchievement($achievement, $disable_cache=true)) {
            // Unlock achievement for user
            UserAchievement::create([
                'user_id' => $this->id,
                'achievement_id' => $achievement->id
            ]);

            // Fire AchievementUnlocked event
            event(new AchievementUnlocked($achievement->name, $this));
        }
    }

    /**
     * Check if the user has this badge
     **/
    public function hasBadge(Badge $badge, bool $disable_cache = false): bool
    {
        if ($disable_cache)
            $this->load('badges');
        return $this->badges && $this->badges->contains($badge);
    }

    /**
     * Unlock badge for user and fire event.
     */
    public function unlockBadge(Badge $badge) : void
    {
        // Check if the user already has the badge
        if (!$this->hasBadge($badge)) {
            // Unlock badge for user
            UserBadge::create([
                'user_id' => $this->id,
                'badge_id' => $badge->id
            ]);

            // Fire BadgeUnlocked event
            event(new BadgeUnlocked($badge->name, $this));
        }
    }

    /**
     * Register lesson to user and fire event.
     */
    public function watchLesson(Lesson $lesson) : void
    {
        // Mark it as "watched" by the user
        $this->lessons()->attach($lesson, ['watched' => 1]);

        // Dispatch the event simulating lesson watching
        event(new LessonWatched($lesson, $this));
    }

    /**
     * Add comment to user and fire event.
     */
    public function sendComment(string $message) : void
    {
        // Mark it as written by the user
        $comment = new Comment([
            'body' => $message
        ]);
        $this->comments()->save($comment);

        // Dispatch the event
        event(new CommentWritten($comment, $this));
    }
}

