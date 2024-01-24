<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Comment;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\Relations\hasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasAchievementsTrait;
use App\Traits\HasBadgesTrait;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasAchievementsTrait, HasBadgesTrait;

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
    public function comments(): hasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons(): belongsToMany
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched(): belongsToMany
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    /**
     * Register lesson to user and fire event.
     */
    public function watchLesson(Lesson $lesson): void
    {
        // Mark it as "watched" by the user
        $this->lessons()->attach($lesson, ['watched' => 1]);

        // Dispatch the event for lesson watched
        event(new LessonWatched($lesson, $this));
    }

    /**
     * Add comment to user and fire event.
     */
    public function sendComment(string $message): void
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

