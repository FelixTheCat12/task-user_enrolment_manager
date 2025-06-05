<?php
namespace App;

use App\Traits\SoftDeletes;

/**
 * Represents a course.
 */
class course
{
    use SoftDeletes;

    /** @var int Unique ID of course. */
    public int $id;

    /** @var string Name of course. */
    public string $title;

    /** @var string|null ISO timestamp when created (optional). */
    public ?string $created_at;

    /** @var string|null ISO timestamp when last updated (optional). */
    public ?string $updated_at;

    /** @var string|null ISO timestamp when soft‐deleted (optional). */
    public ?string $deleted_at;

    protected const FILE_PATH = __DIR__ . '/../data/courses.json';

    /**
     * Populate the object.
     *
     * @param int $id
     * @param string $title
     */
    public function __construct(
        int $id,
        string $title,
        ?string $created_at = null,
        ?string $updated_at = null,
        ?string $deleted_at = null
    ) {
        $this->id         = $id;
        $this->title      = $title;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->deleted_at = $deleted_at;
    }

    public static function filePath(): string
    {
        return self::FILE_PATH;
    }

    /**
     * Load and decode all courses from courses.json.
     *
     * @return array<int, array{id: int, title: string}>
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
     * Save the provided courses array back to courses.json.
     *
     * @param array<int, array{id: int, title: string}> $allcourses
     * @return void
     */
    public static function saveAll(array $allCourses): void
    {
        $filePath = self::filePath();
        file_put_contents($filePath, json_encode($allCourses, JSON_PRETTY_PRINT));
    }

    /**
     * Load and return a single course record by its ID.
     *
     * @param int $id
     * @return course|null Returns a course object if found, or null if not.
     */
    public static function findById(int $id): ?course
    {
        $allCourses = self::loadAll();
        foreach ($allCourses as $item) {
            if ($item['id'] === $id) {
                return new course(
                    $item['id'],
                    $item['title'],
                    $item['created_at'] ?? null,
                    $item['updated_at'] ?? null,
                    $item['deleted_at'] ?? null
                );
            }
        }
        return null;
    }
}
