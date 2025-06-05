<?php
namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../user.php';
require_once __DIR__ . '/../course.php';
require_once __DIR__ . '/../enrolment_manager.php';
require_once __DIR__ . '/../Logger/Log.php';

use App\course as Course;
use App\enrolment_manager as Enrolment;
use App\Logger\Log;
use App\user as User;
use Carbon\Carbon;
use Exception;

class EnrolmentController
{
    public static function createEnrolment(int $userId, int $courseId): void
    {
        try {
            if (User::findById($userId) === null) {
                echo "User with ID {$userId} does not exist";
                return;
            }

            if (Course::findById($courseId) === null) {
                echo "Course with ID {$courseId} does not exist";
                return;
            }

            $enrolments = Enrolment::loadAll();
            foreach ($enrolments as $enrolment) {
                if ($enrolment['user_id'] === $userId && $enrolment['course_id'] === $courseId) {
                    echo "User {$userId} is already enrolled in course {$courseId}";
                    return;
                }
            }

            $now          = Carbon::now();
            $enrolments[] = [
                'user_id'    => $userId,
                'course_id'  => $courseId,
                'created_at' => $now,
            ];

            Enrolment::saveAll($enrolments);
            echo "Enrolled user {$userId} in course {$courseId}";
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function getAllEnrolments(): void
    {
        try {
            $enrolments = Enrolment::loadAll();
            if (empty($enrolments)) {
                echo "No enrolments found";
                return;
            }

            echo "\nUser ID | User Name         | Course ID | Course Title\n";
            echo str_repeat('-', 60) . "\n";

            foreach ($enrolments as $enrolment) {
                $user        = User::findById($enrolment['user_id']);
                $course      = Course::findById($enrolment['course_id']);
                $userName    = $user?->name ?? '(deleted user)';
                $courseTitle = $course?->title ?? '(deleted course)';

                printf(
                    "%-7d | %-18s | %-9d | %s\n",
                    $enrolment['user_id'],
                    $userName,
                    $enrolment['course_id'],
                    $courseTitle
                );
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function getEnrolmentsByUserId(int $userId): void
    {
        try {
            $enrolments = Enrolment::loadAll();
            $found      = false;

            echo "\nEnrolments for User ID {$userId}:\n";
            echo "Course ID | Course Title\n";
            echo str_repeat('-', 40) . "\n";

            foreach ($enrolments as $enrolment) {
                if ($enrolment['user_id'] === $userId) {
                    $found  = true;
                    $course = Course::findById($enrolment['course_id']);
                    $title  = $course?->title ?? '(deleted course)';
                    printf("%-9d | %s\n", $enrolment['course_id'], $title);
                }
            }

            if (! $found) {
                echo "No enrolments found for user {$userId}.\n";
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function getEnrolmentsByCourseId(int $courseId): void
    {
        try {
            $enrolments = Enrolment::loadAll();
            $found      = false;

            echo "\nEnrolments for Course ID {$courseId}:\n";
            echo "User ID | User Name\n";
            echo str_repeat('-', 40) . "\n";

            foreach ($enrolments as $enrolment) {
                if ($enrolment['course_id'] === $courseId) {
                    $found = true;
                    $user  = User::findById($enrolment['user_id']);
                    $name  = $user?->name ?? '(deleted user)';
                    printf("%-7d | %s\n", $enrolment['user_id'], $name);
                }
            }

            if (! $found) {
                echo "No enrolments found for course {$courseId}.\n";
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function deleteEnrolment(int $userId, int $courseId): void
    {
        try {
            $enrolments = Enrolment::loadAll();
            $found      = false;

            foreach ($enrolments as $index => $enrolment) {
                if ($enrolment['user_id'] === $userId && $enrolment['course_id'] === $courseId) {
                    unset($enrolments[$index]);
                    $found = true;
                    break;
                }
            }

            if (! $found) {
                echo "No enrolment found for user {$userId} in course {$courseId}";
                return;
            }

            Enrolment::saveAll(array_values($enrolments));
            echo "Removed enrolment of user {$userId} from course {$courseId}";
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}
