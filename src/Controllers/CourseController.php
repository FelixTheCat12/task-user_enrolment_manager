<?php
namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../course.php';
require_once __DIR__ . '/../Logger/Log.php';

use App\course as Course;
use App\Logger\Log;
use Carbon\Carbon;
use Exception;

class CourseController
{
    public static function getAllCourses(): void
    {
        try {
            $rawCourses = Course::loadAll();
            $courses    = [];

            foreach ($rawCourses as $rawCourse) {
                $courses[] = new Course($rawCourse['id'], $rawCourse['title'], $rawCourse['created_at'], $rawCourse['updated_at']);
            }

            echo "\nID   | Title               | Created At          | Updated At\n";
            echo str_repeat('-', 70) . "\n";

            foreach ($courses as $course) {
                printf(
                    "%-4d | %-18s | %-19s | %-19s\n",
                    $course->id,
                    $course->title,
                    Carbon::parse($course->created_at)->format('F j y'),
                    Carbon::parse($course->updated_at)->format('F j y'),
                );
            }

        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function getCourseById(int $courseId)
    {
        try {
            $rawCourses = Course::loadAll();

            foreach ($rawCourses as $rawCourse) {
                if ($rawCourse['id'] === $courseId) {
                    $course = new Course(
                        $rawCourse['id'],
                        $rawCourse['title'],
                        $rawCourse['created_at'] ?? null,
                        $rawCourse['updated_at'] ?? null,
                        $rawCourse['deleted_at'] ?? null
                    );

                    echo "\nID   | Title               | Created At          | Updated At\n";
                    echo str_repeat('-', 70) . "\n";

                    $created = $course->created_at
                    ? Carbon::parse($course->created_at)->format('F j, Y')
                    : '—';
                    $updated = $course->updated_at
                    ? Carbon::parse($course->updated_at)->format('F j, Y')
                    : '—';

                    printf(
                        "%-4d | %-18s | %-19s | %-19s\n\n",
                        $course->id,
                        $course->title,
                        $created,
                        $updated
                    );
                    return;
                }
            }

            echo "Course with ID {$courseId} not found.";
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function createCourse(string $title): void
    {
        try {

            $courses = Course::loadAll();

            $id = 0;
            foreach ($courses as $course) {
                if ($course['id'] > $id) {
                    $id = $course['id'];
                }
            }

            $now = Carbon::now();

            $courses[] = [
                'id'         => $id + 1,
                'title'      => $title,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ];

            Course::saveAll($courses);

            echo "Course created successfully";
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function updateCourseById(int $courseId, string $newTitle): void
    {
        try {
            $allCourses = Course::loadAll();
            $found      = false;

            foreach ($allCourses as &$item) {
                if ($item['id'] === $courseId) {

                    $item['title'] = $newTitle;

                    $item['updated_at'] = Carbon::now();
                    $found              = true;
                    break;
                }
            }
            unset($item);

            if (! $found) {
                echo "Course with ID {$courseId} not found.\n";
                return;
            }

            Course::saveAll($allCourses);

            echo "Course ID {$courseId} updated successfully to '{$newTitle}'";
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function deleteCourseById(int $courseId)
    {
        try {
            $course = Course::findById($courseId);
            if ($course === null) {
                echo "Course with ID {$courseId} not found.\n";
                return;
            }

            $allCourses = Course::loadAll();
            $now        = Carbon::now();
            $found      = false;

            foreach ($allCourses as &$item) {
                if ($item['id'] === $courseId) {
                    $item['updated_at'] = $now;
                    $item['deleted_at'] = $now; // Soft Deletes.
                    $found              = true;
                    break;
                }
            }
            unset($item);

            if (! $found) {
                echo "Course ID {$courseId} not found.\n";
                return;
            }

            Course::saveAll($allCourses);
            echo "Course ID {$courseId} soft deleted successfully.\n";
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}
