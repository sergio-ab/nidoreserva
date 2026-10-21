<?php

namespace Sergi\Misterplan\Database;

use PDO;
use Dotenv\Dotenv;

class DatabaseConnection
{
    // Stores the active database connection.
    private PDO $connection;

    public function __construct()
    {
        // Get the root directory of the project.
        $projectRoot = dirname(__DIR__, 2);

        // Load environment variables from the .env file.
        $dotenv = Dotenv::createImmutable($projectRoot);
        $dotenv->safeLoad();

        // Read database configuration from environment variables.
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $database = $_ENV['DB_NAME'] ?? '';
        $username = $_ENV['DB_USER'] ?? '';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        // Ensure the required database configuration is available.
        if ($database === '' || $username === '') {
            throw new \RuntimeException(
                'The database name or username is missing from the .env file.'
            );
        }

        // Build the Data Source Name (DSN) for the MySQL/MariaDB connection.
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        // Create the PDO connection with explicit error handling and fetch settings.
        $this->connection = new PDO(
            $dsn,
            $username,
            $password,
            [
                // Throw exceptions when a database operation fails.
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                // Return query results as associative arrays by default.
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                // Use native prepared statements when supported by the driver.
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    // Return the PDO connection so other classes can reuse it.
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
