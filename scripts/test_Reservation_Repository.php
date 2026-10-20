
<?php

use Sergi\Misterplan\Database\DatabaseConnection;
use Sergi\Misterplan\Repository\ReservationRepository;

require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    $databaseConnection = new DatabaseConnection();

    $reservationRepository = new ReservationRepository(
        $databaseConnection
    );

    echo "Reservation repository initialized successfully!" . PHP_EOL;
} catch (Throwable $exception) {
    echo "Reservation repository initialization failed: "
        . $exception->getMessage() . PHP_EOL;

    exit(1);
}
