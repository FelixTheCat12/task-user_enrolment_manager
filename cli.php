<?php
declare (strict_types = 1);
require_once __DIR__ . '/src/Controllers/UserController.php';
require_once __DIR__ . '/src/Controllers/CourseController.php';
require_once __DIR__ . '/src/Controllers/EnrolmentController.php';
require_once __DIR__ . '/src/Logger/Log.php';

use App\Controllers\CourseController;
use App\Controllers\EnrolmentController;
use App\Controllers\UserController;
use App\Logger\Log;
Log::info('CLI started');

/**
 * Data rention
 */
function initializeDataStore(): void
{
    $dataDir = __DIR__ . '/data';
    if (! is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }

    $files = [
        'users.json',
        'courses.json',
        'enrolments.json',
    ];

    foreach ($files as $fileName) {
        $filePath = $dataDir . '/' . $fileName;
        if (! file_exists($filePath)) {
            file_put_contents($filePath, json_encode([], JSON_PRETTY_PRINT));
        }
    }
}

function clearScreen(): void
{
    if (PHP_OS_FAMILY === 'Windows') {
        system('cls');
    } else {
        system('clear');
    }
}

function prompt(string $message): string
{
    echo $message;
    $input = trim(fgets(STDIN));
    return $input;
}

function mainMenu(): void
{
    echo "======DIDASKO FELIX CLI========\n";
    echo "1) User\n";
    echo "2) Courses\n";
    echo "3) Enrolment\n";
    echo "0) Exit\n";
    echo "======DIDASKO FELIX CLI========\n";
}

function actionMenu(string $entityName): void
{
    echo "========{$entityName} Actions========\n";
    echo "1) View All\n";
    echo "2) View by ID\n";
    echo "3) Create\n";
    echo "4) Update\n";
    echo "5) Delete\n";
    echo "0) Back to Main Menu\n";
    echo "==============================\n";
}

function handleEntity(string $entityName): void
{
    while (true) {
        clearScreen();

        actionMenu($entityName);

        $action = prompt("Select an action (0-5): ");

        switch ($action) {
            case '1':
                echo "-- All {$entityName} --\n";

                if ($entityName === 'User') {
                    UserController::getAllUsers();
                }

                if ($entityName === 'Course') {
                    CourseController::getAllCourses();
                }

                break;
            case '2':
                echo "-- View {$entityName} By ID --\n";

                if ($entityName === 'User') {
                    $id = prompt('Enter User id: ');
                    UserController::getUserById((integer) $id);
                }

                if ($entityName === 'Course') {
                    $id = prompt('Enter Course id: ');
                    CourseController::getCourseById((integer) $id);
                }

                break;
            case '3':
                echo "-- Create {$entityName} --\n";

                do {
                    if ($entityName === 'User') {
                        $input = prompt("Enter a new user name (can't be empty): ");
                    } else if ($entityName === 'Course') {
                        $input = prompt("Enter new course title (can't be empty): ");
                    }
                    $input = trim($input);
                    if ($input === '') {
                        echo "Input can't be empty. Please try again.\n";
                    }
                } while ($input === '');
                if ($entityName === 'User') {
                    UserController::createUser($input);
                } else if ($entityName === 'Course') {
                    CourseController::createCourse($input);
                }

                break;
            case '4':
                echo "-- Update {$entityName} By ID --\n";

                if ($entityName === 'User') {
                    $id      = prompt("Enter User id to update: ");
                    $newName = prompt("Enter new name: ");
                    UserController::updateUserById((integer) $id, $newName);
                } else if ($entityName === 'Course') {
                    $id       = prompt("Enter Course id to update: ");
                    $newTitle = prompt("Enter new title: ");
                    CourseController::updateCourseById((integer) $id, $newTitle);
                }

                break;
            case '5':
                echo "-- Delete {$entityName} by ID --\n";

                if ($entityName === 'User') {
                    $id = prompt("Enter User id to delete: ");

                    UserController::deleteUserById((integer) $id);
                }

                if ($entityName === 'Course') {
                    $id = prompt("Enter Course id to delete: ");

                    CourseController::deleteCourseById((integer) $id);
                }

                break;
            case '0':

                return;
            default:
                echo "Invalid selection. Please choose 0-5.\n";
        }

        echo "\nPress Enter to continue...";
        fgets(STDIN);
    }
}

/**
 * Special Cases for Enrolment ONLY
 */
function handleEnrolmentMenu(): void
{
    while (true) {
        clearScreen();
        echo "=== Enrolment Menu ===\n";
        echo "1) Enrol a User into a Course\n";
        echo "2) List Enrolments\n";
        echo "3) Delete an Enrolment\n";
        echo "0) Back to Main Menu\n";

        $action = prompt("Select an action (0-3): ");

        switch ($action) {
            case '1':

                echo "-- Enrol a User --\n";

                UserController::getAllUsers();
                $uid = (int) prompt("Enter user ID to enrol: ");

                CourseController::getAllCourses();
                $cid = (int) prompt("Enter course ID to enrol into: ");

                EnrolmentController::createEnrolment($uid, $cid);
                break;

            case '2':
                echo "-- All Enrolments --\n";
                // Sub‐menu for ENROLMENT VIEWING
                while (true) {
                    clearScreen();
                    echo "=== List Enrolments ===\n";
                    echo "1) View ALL Enrolments\n";
                    echo "2) View by User ID\n";
                    echo "3) View by Course ID\n";
                    echo "0) Back to Enrolment Menu\n";
                    echo "=======================\n";

                    $sub = prompt("Select an option (0-3): ");

                    switch ($sub) {
                        case '1':
                            EnrolmentController::getAllEnrolments();
                            break;

                        case '2':
                            UserController::getAllUsers();
                            $uid = (int) prompt("Enter User ID to filter: ");
                            EnrolmentController::getEnrolmentsByUserId($uid);
                            break;

                        case '3':
                            CourseController::getAllCourses();
                            $cid = (int) prompt("Enter Course ID to filter: ");
                            EnrolmentController::getEnrolmentsByCourseId($cid);
                            break;

                        case '0':
                            break 2;

                        default:
                            echo "Invalid selection. Please choose 0-3.\n";
                    }

                    echo "\nPress Enter to continue...";
                    fgets(STDIN);
                }
                break;

            case '3':

                echo "-- Delete Enrolment --\n";
                $uid = (int) prompt("Enter user ID to remove from a course: ");
                $cid = (int) prompt("Enter course ID to remove: ");
                EnrolmentController::deleteEnrolment($uid, $cid);
                break;

            case '0':
                return;

            default:
                echo "Invalid selection. Please choose 0-3.\n";
        }

        echo "\nPress Enter to continue...";
        fgets(STDIN);
    }
}

function runCLI(): void
{
    initializeDataStore();

    while (true) {
        clearScreen();
        mainMenu();

        $choice = prompt("Select an option (0-3): ");

        switch ($choice) {
            case '1':
                handleEntity('User');
                break;
            case '2':
                handleEntity('Course');
                break;
            case '3':
                handleEnrolmentMenu();
                break;
            case '0':
                echo "Exiting... Goodbye!\n";
                exit(0);
            default:
                echo "Invalid selection. Please choose 0-3.\n";
        }
    }
}

runCLI();
