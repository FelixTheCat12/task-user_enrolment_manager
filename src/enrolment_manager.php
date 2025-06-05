<?php
namespace App;

require_once __DIR__ . '/../Logger/Log.php';

use App\Helpers\Helpers;
use App\Logger\Log;
use App\Traits\SoftDeletes;
use InvalidArgumentException;
use RuntimeException;

/**
 * Manage enrolments of users in courses.
 */
class enrolment_manager
{
    use Helpers, SoftDeletes;

    /** @var array List of user objects. */
    private array $users = [];

    /** @var array List of course objects. */
    private array $courses = [];

    /** @var array List of enrolments. */
    private array $enrolments = [];

    protected const FILE_PATH = __DIR__ . '/../data/enrolments.json';

    /**
     * Populate valid users and courses in the object's memory to use for enrolments.
     *
     * @param array $users Array of user objects.
     * @param array $courses Array of course objects.
     */
    public function __construct(array $users, array $courses)
    {
        // We populate this class with user and course data instead of relying on a database to simplify the application.
        $this->users   = $users;
        $this->courses = $courses;
    }

    /**
     * Enrol a user in a course.
     *
     * @param int $userid ID of user object.
     * @param int $courseid ID of course object.
     * @return void
     */
    public function enrol_user(int $userid, int $courseid): void
    {
        if (! $this->userExists($userid)) {
            Log::error("User with ID {$userid} doesn't exist");
            throw new InvalidArgumentException("User with ID {$userid} doesn't exist");
        }
        if (! $this->courseExists($courseid)) {
            Log::error("Course ID {$courseid} doesn't exist");
            throw new InvalidArgumentException("Course ID {$courseid} doesn't exist");
        }
        if ($this->isAlreadyEnrolled($userid, $courseid)) {
            Log::error("User {$userid} is already enrolled in course {$courseid}");
            throw new RuntimeException("User {$userid} is already enrolled in course {$courseid}");
        }

        foreach ($this->enrolments as [$enrolUserId, $enrolCourseId]) {
            if ($enrolUserId === $userid && $enrolCourseId === $courseid) {
                throw new RuntimeException("User {$userid} is already enrolled in this course {$courseid}");
            }
        }

        $this->enrolments[] = [$userid, $courseid];

    }

    /**
     * Unenrol a user from a course.
     *
     * @param int $userid ID of user object.
     * @param int $courseid ID of user object.
     * @return void
     */
    public function unenrol_user(int $userid, int $courseid): void
    {
        if (! $this->userExists($userid)) {
            Log::error("User with ID {$userid} doesn't exist}");

            throw new InvalidArgumentException("User with ID {$userid} doesn't exist");
        }
        if (! $this->courseExists($courseid)) {
            Log::error("Course ID {$courseid} doesn't exist");

            throw new InvalidArgumentException("Course ID {$courseid} doesn't exist");
        }

        $enrolmentIndex = $this->findEnrolmentIndex($userid, $courseid);
        if ($enrolmentIndex === null) {
            Log::error("User {$userid} is not enrolled in course {$courseid}");
            throw new RuntimeException("User {$userid} is not enrolled in course {$courseid}");
        }

        array_splice($this->enrolments, $enrolmentIndex, 1);
    }

    /**
     * Get all courses a user is enrolled in.
     *
     * @param int $userid ID of user object.
     * @return array
     */
    public function get_user_courses(int $userid): array
    {
        if (! $this->userExists($userid)) {
            Log::error("User with ID {$userid} doesn't exist");
            throw new InvalidArgumentException("User with ID {$userid} doesn't exist");
        }

        $enrolledCourses = [];
        foreach ($this->enrolments as [$enrolUserId, $enrolCourseId]) {
            if ($enrolUserId === $userid) {
                $enrolledCourses[] = $enrolCourseId;
            }
        }

        $res = [];

        foreach ($this->courses as $course) {
            if (in_array($course->id, $enrolledCourses, true)) {
                $res[] = $course;
            }
        }
        return $res;
    }

    public static function filePath(): string
    {
        return self::FILE_PATH;
    }

    /**
     * Fetchy and decode all enrolments from enrolments.json
     */
    public static function loadAll(): array
    {
        $filePath = self::filePath();
        $json     = file_get_contents($filePath);
        if (trim($json) === '') {
            return [];
        }

        $response = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return self::filterDeleted($response);
    }

    /**
     * Save enrolments back to enrolments
     * @return void
     */
    public static function saveAll(array $allEnrolments): void
    {
        $filePath = self::filePath();
        file_put_contents($filePath, json_encode($allEnrolments, JSON_PRETTY_PRINT));
    }
}
