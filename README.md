# User Enrolment Manager – Take Home Task

## 🧠 Task Overview

You’ve been provided with a simple PHP framework simulating Moodle-like users and courses. Your task is to implement an `enrolment_manager` class that supports enrolling and unenrolling users from courses and retrieving enrolment information.

## 🎯 Objectives

- Implement the following methods in `enrolment_manager.php`:
  - `public function enrol_user(int $userid, int $courseid): void`
  - `public function unenrol_user(int $userid, int $courseid): void`
  - `public function get_user_courses(int $userid): array`

- Handle and guard against:
  - Duplicate enrolments
  - Unenrolling users not enrolled in a course
  - Invalid user or course IDs

- Ensure your implementation:
  - Uses the provided `user` and `course` classes
  - Passes all PHPUnit tests
  - Has unit tests to cover the above scenarios
  - All commits are logical and include a sane message
    - https://www.conventionalcommits.org/en/v1.0.0/

- There is no need to include a datasource like a database in the application.

## ✅ Requirements

- PHP 8.0+
- PHPUnit (via Composer)
- Your code **must be tested** with `enrolment_manager_test.php`

## 📦 Setup

"April.Porter@didasko-online.com" <
April.Porter@didasko-online.com
Andrew.madden@didasko-online.com
>
"Andrew.madden@didasko-online.com" <
>
1. **Install dependencies**
   Run the following to install PHPUnit:

   ```bash
   composer install
   ```

2. **Run the tests**:

   ```bash
   ./vendor/bin/phpunit
   ```

## 🚀 Submission

- Fork this repository
- Complete the task
- Push your changes to your fork
- Submit your GitHub repo link back to us

## 🧪 Bonus (Optional)

- Create a CLI entry point (e.g., `cli.php`) that lets a user enrol/unenrol using command-line input
- Add logging or exception handling for unexpected scenarios

---

Good luck and have fun!
