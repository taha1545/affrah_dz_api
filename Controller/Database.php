<?php

use Dotenv\Dotenv;

// create connection with db using .env info 

class Database
{
    private static $conn = null;

    public static function getConnection()
    {
        if (self::$conn === null) {
            //
            $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
            $dotenv->load();
            //
            $dbHost = $_ENV['DB_HOST'] ?? 'db';
            $dbUser = $_ENV['DB_USER'] ?? 'root';
            $dbPassword = $_ENV['DB_PASSWORD'] ?? 'rootpassword';
            $dbName = $_ENV['DB_NAME'] ?? 'affrah';

            self::$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

            if (self::$conn->connect_error) {
                die("Database connection failed: " . self::$conn->connect_error);
            }
        }

        return self::$conn;
    }

    public static function closeConnection()
    {
        if (self::$conn !== null) {
            self::$conn->close();
            self::$conn = null;
        }
    }
}
