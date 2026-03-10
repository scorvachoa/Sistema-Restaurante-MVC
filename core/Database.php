<?php
class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $db = App::config('db');
            $dsn = "mysql:host={$db['host']};dbname={$db['db']};charset={$db['charset']}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            self::$instance = new PDO($dsn, $db['user'], $db['pass'], $options);
        }
        return self::$instance;
    }
}
