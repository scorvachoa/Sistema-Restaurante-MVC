<?php
class App
{
    public static array $config = [];

    public static function loadConfig(): void
    {
        self::$config['app'] = require __DIR__ . '/../config/app.php';
        self::$config['db'] = require __DIR__ . '/../config/database.php';
        date_default_timezone_set(self::$config['app']['timezone']);
    }

    public static function config(string $group, string $key = null)
    {
        if (!isset(self::$config[$group])) {
            return null;
        }
        if ($key === null) {
            return self::$config[$group];
        }
        return self::$config[$group][$key] ?? null;
    }
}
