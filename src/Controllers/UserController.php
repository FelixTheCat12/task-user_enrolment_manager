<?php
namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../user.php';
require_once __DIR__ . '/../Logger/Log.php';

use App\Logger\Log;
use App\user as User;
use Carbon\Carbon;
use Exception;

class UserController
{

    public static function getAllUsers(): void
    {
        try {
            $rawUsers = User::loadAll();
            $users    = [];

            foreach ($rawUsers as $rawUser) {
                $users[] = new User($rawUser['id'], $rawUser['name'], $rawUser['created_at'], $rawUser['updated_at']);
            }

            echo "\nID   | Name               | Created At          | Updated At\n";
            echo str_repeat('-', 70) . "\n";

            foreach ($users as $user) {
                printf(
                    "%-4d | %-18s | %-19s | %-19s\n",
                    $user->id,
                    $user->name,
                    Carbon::parse($user->created_at)->format('F j y'),
                    Carbon::parse($user->updated_at)->format('F j y'),
                );
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function getUserById(int $userId)
    {
        try {

            $rawUsers = User::loadAll();

            foreach ($rawUsers as $rawUser) {
                if ($rawUser['id'] === $userId) {

                    $user = new User(
                        $rawUser['id'],
                        $rawUser['name'],
                        $rawUser['created_at'] ?? null,
                        $rawUser['updated_at'] ?? null,
                        $rawUser['deleted_at'] ?? null
                    );

                    echo "\nID   | Name               | Created At          | Updated At\n";
                    echo str_repeat('-', 70) . "\n";

                    $created = $user->created_at
                    ? Carbon::parse($user->created_at)->format('F j, Y')
                    : '—';
                    $updated = $user->updated_at
                    ? Carbon::parse($user->updated_at)->format('F j, Y')
                    : '—';

                    printf(
                        "%-4d | %-18s | %-19s | %-19s\n\n",
                        $user->id,
                        $user->name,
                        $created,
                        $updated
                    );
                    return;
                }
            }

            echo "User with ID {$userId} not found";
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function createUser(string $name): void
    {
        try {
            $users = User::loadAll();

            $id = 0;
            foreach ($users as $user) {
                if ($user['id'] > $id) {
                    $id = $user['id'];
                }
            }

            $now = Carbon::now();

            $users[] = [
                'id'         => $id + 1,
                'name'       => $name,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ];

            User::saveAll($users);

            echo "User created successfully";
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function updateUserById(int $userId, string $newName): void
    {
        try {
            $allUsers = User::loadAll();
            $found    = false;

            foreach ($allUsers as &$item) {
                if ($item['id'] === $userId) {
                    $item['name'] = $newName;

                    $item['updated_at'] = Carbon::now();
                    $found              = true;
                    break;
                }
            }
            unset($item);

            if (! $found) {
                echo "User with ID {$userId} not found.\n";
                return;
            }

            User::saveAll($allUsers);

            echo "User ID {$userId} updated successfully to '{$newName}'";
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public static function deleteUserById(int $userId)
    {
        try {
            $user = User::findById($userId);
            if ($user === null) {
                echo "User with ID {$userId} not found.\n";
                return;
            }

            $allUsers = User::loadAll();
            $now      = Carbon::now();
            $found    = [];

            foreach ($allUsers as &$item) {
                if ($item['id'] === $userId) {
                    $item['updated_at'] = $now;
                    $item['deleted_at'] = $now; // Soft Deletes.
                    $found              = true;
                    break;
                }
            }
            unset($item);

            if (! $found) {
                echo "User ID {$userId} not found.\n";
                return;
            }

            User::saveAll($allUsers);
            echo "User ID {$userId} deleted successfully";

        } catch (Exception $e) {
            Log::error($e);
        }
    }
}
