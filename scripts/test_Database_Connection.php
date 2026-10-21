
<?php

use Sergi\Misterplan\Database\DatabaseConnection;

// Load Composer's autoloader to register project classes and dependencies.
require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    // Create the database connection.
    $databaseConnection = new DatabaseConnection();

    // Retrieve the PDO connection.
    $connection = $databaseConnection->getConnection();

    // Check that the connection is active.
    echo "Database connection successful!" . PHP_EOL;
} catch (Throwable $exception) {
    // Display the error during local development.
    echo "Database connection failed: " . $exception->getMessage() . PHP_EOL;
    exit(1);
}
