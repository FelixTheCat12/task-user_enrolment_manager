<?php
namespace App;

use App\Traits\SoftDeletes;

/**
 * Represents a user.
 */
class user
{
    use SoftDeletes;

    /** @var int Unique ID of user. */
    public int $id;

    /** @var string Name of user. */
    public string $name;

    /** @var string|null ISO timestamp when created (optional). */
    public ?string $created_at;

    /** @var string|null ISO timestamp when last updated (optional). */
    public ?string $updated_at;

    /** @var string|null ISO timestamp when soft‐deleted (optional). */
    public ?string $deleted_at;

    protected const FILE_PATH = __DIR__ . '/../data/users.json';

    /**
     * Populate the object.
     *
     * @param int $id
     * @param string $name
     * @param string|null $created_at
     * @param string|null $updated_at
     * @param string|null $deleted_at
     */
    public function __construct(
        int $id,
        string $name,
        ?string $created_at = null,
        ?string $updated_at = null,
        ?string $deleted_at = null
    ) {
        $this->id         = $id;
        $this->name       = $name;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->deleted_at = $deleted_at;
    }

    public static function filePath(): string
    {
        return self::FILE_PATH;
    }

    /**
     * Load and decode all users from users.json.
     *
     * @return array<int, array{id: int, name: string}>
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
     * Save the provided users array back to users.json.
     *
     * @param array<int, array{id: int, name: string}> $allUsers
     * @return void
     */
    public static function saveAll(array $allUsers): void
    {
        $filePath = self::filePath();
        file_put_contents($filePath, json_encode($allUsers, JSON_PRETTY_PRINT));
    }

    /**
     * Load and return a single user record by its ID.
     *
     * @param int $id
     * @return user|null Returns a user object if found, or null if not.
     */
    public static function findById(int $id): ?user
    {
        $allUsers = self::loadAll();
        foreach ($allUsers as $item) {
            if ($item['id'] === $id) {
                return new user(
                    $item['id'],
                    $item['name'],
                    $item['created_at'] ?? null,
                    $item['updated_at'] ?? null,
                    $item['deleted_at'] ?? null
                );
            }
        }
        return null;
    }
}
