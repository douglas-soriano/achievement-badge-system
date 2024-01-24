# Assessment: Achievement and Badge System

This project implements an achievement and badge system for a hypothetical learning platform. Users can earn achievements based on their actions and progress, and they can unlock badges as they accumulate achievements.

**Requirements:**
*```PHP >= 8.1```  is required, and you need to have a created ```database```. Also, dont forget to update your ```composer```.*



## Purpose:

The goal of this assessment was to create a well-structured and flexible achievement and badge system that can easily be scalable and support multiples achievements and badges.




## Installation:

**1. Clone the repository:**
```
git clone https://github.com/douglas-soriano/achievement-badge-system.git .
```

**2. Install dependencies:**
```
composer install
```

**3. Configure environment variables:**
```
cp .env.example .env
```

Configure the DB variables: DB_DATABASE, DB_USERNAME, DB_PASSWORD.

**4. Install the application:**
```
php artisan app:refresh
```

This command will clear the database, run migrations, and populate it with initial data.



## Achievements and Badges

The system currently offers several achievements and badges to motivate users and recognize their progress.

**Achievements:**

| Achievement | Message |   Code |
| ----------- | ------- | ---- |
| ***Lessons*** |  |     |
| First Lesson Watched | Congratulations on watching your first lesson! | first_lesson_watched |
| 5 Lessons Watched | Keep learning! You've watched 5 lessons. | 5_lessons_watched |
| 10 Lessons Watched | You're on a roll! 10 lessons watched already! | 10_lessons_watched |
| 25 Lessons Watched | Wow, 25 lessons watched! Keep up the good work! | 25_lessons_watched |
| 50 Lessons Watched | You're a learning machine! 50 lessons watched! | 50_lessons_watched |
| ***Comments*** |  |    |
| First Comment Written | Welcome to the community! You wrote your first com... | first_comment_written |
| 3 Comments Written | You're engaging with the community! 3 comments wri... | 3_comments_written |
| 5 Comments Written | Keep sharing your thoughts! You've written 5 comme... | 5_comments_written |
| 10 Comments Written | You're an active community member! 10 comments wri... | 10_comments_written |
| 20 Comments Written | Wow, you're a community star! 20 comments written! | 20_comments_written |

**Badges:**

| Badge | Message | Achievements to Unlock |
| ----- | ------- | ------------ |
| Beginner | Welcome to the learning journey! | 0 |
| Intermediate | You're making progress! Keep learning and engaging... | 4 |
| Advanced | You're on a roll! Keep up the impressive learning ... | 8 |
| Master | Congratulations! You've achieved mastery in learni... | 10 |



## Main Files:

#### [app/Services/AchievementService.php:](app/Services/AchievementService.php)
Handles tasks related to achievements, including checking if a user meets requirements, unlocking achievements, and retrieving user achievements.


#### [app/Services/BadgeService.php:](app/Services/BadgeService.php)
Manages badges, including determining user progress towards the next badge, unlocking badges, and retrieving user badge information.

#### [app/Traits/HasAchievementsTrait.php:](app/Traits/HasAchievementsTrait.php)
This trait provides methods for managing a user's achievements. It includes the relationship, achievements checks, and methods to unlock achievements.

#### [app/Traits/HasBadgesTrait.php:](app/Traits/HasBadgesTrait.php)
This trait provides methods for managing a user's badges. It includes the relationship, badges checks, and methods to unlock badges.



## Unlocking Achievements:

Users can unlock achievements by fulfilling specific criteria defined for each achievement. These criteria could involve actions like completing lessons or writing comments. The `AchievementService` handles checking if a user meets the requirements and updates the user's achievements accordingly.

You can call the `unlockAchievement` method in the `User` model, inherited from `HasAchievementsTrait`.

```
$user->unlockAchievement(Achievement $achievement));
```

## Unlocking Badges:

Badges are typically awarded based on accumulated achievements. The `BadgeService` tracks user progress towards badges and automatically unlocks them when the necessary achievements are attained.

You can call the `unlockBadge` method in the `User` model, inherited from `HasBadgesTrait`.

```
$user->unlockBadge(Badge $badge));
```


## Events:

**CommentWritten:** Fired whenever a new comment is posted. It is used to trigger actions like awarding achievements for writing comments.

**LessonWatched:** Fired whenever a new lesson is watched. It is used to trigger actions like awarding achievements for watching lessons.

**AchievementUnlocked:** Fired whenever a new achievement is unlocked by the user.

**BadgeUnlocked:** Fired whenever a new badge is unlocked by the user.


## Testing:

The application includes unit and feature tests to ensure its functionality and correctness. You can run the tests with the following command:

```
php artisan test
```

#### Tests Created:

- Unit tests for individual services (AchievementService and BadgeService).
- Feature tests for user achievement and badge interactions (e.g., checking /users/1/achievements endpoint).
- Test for events and its handling (CommentWritten and LessonWatched).
- Checking for each achievement requirements, including multiple requirements.