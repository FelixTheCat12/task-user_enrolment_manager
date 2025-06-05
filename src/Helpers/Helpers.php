<?php
namespace App\Helpers;

trait Helpers
{
    private function userExists(int $userid): bool
    {
        foreach ($this->users as $user) {
            if ($user->id === $userid) {
                return true;
            }
        }
        return false;
    }

    private function courseExists(int $courseid): bool
    {
        foreach ($this->courses as $c) {
            if ($c->id === $courseid) {
                return true;
            }
        }
        return false;
    }

    private function isAlreadyEnrolled(int $userid, int $courseid): bool
    {
        foreach ($this->enrolments as [$userId, $courseId]) {
            if ($userId === $userid && $courseId === $courseid) {
                return true;
            }
        }
        return false;
    }

    private function findEnrolmentIndex(int $userid, int $courseid): ?int
    {
        foreach ($this->enrolments as $i => [$userId, $courseId]) {
            if ($userId === $userid && $courseId === $courseid) {
                return $i;
            }
        }
        return null;
    }
}
