<?php
namespace App\Logger;

class Log
{
    protected const LOG_DIR  = __DIR__ . '/../../logs';
    protected const LOG_FILE = self::LOG_DIR . '/didasko.log';

    public static function init(): void
    {
        if (! is_dir(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0755, true);
        }
        if (! file_exists(self::LOG_FILE)) {
            file_put_contents(self::LOG_FILE, '');
        }
    }

    public static function info(string $message): void
    {
        self::writeLine('INFO', $message);
    }

    public static function error(string $message): void
    {
        self::writeLine('ERROR', $message);
    }

    protected static function writeLine(string $level, string $message): void
    {
        self::init();

        $timestamp = date('Y-m-d H:i:s');
        $line      = "[{$timestamp}] {$level}: {$message}" . PHP_EOL;

        file_put_contents(self::LOG_FILE, $line, FILE_APPEND);
    }
}
