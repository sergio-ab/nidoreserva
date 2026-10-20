
<?php

use Sergi\Misterplan\Database\DatabaseConnection;
use Sergi\Misterplan\Repository\ReservationRepository;

require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    $databaseConnection = new DatabaseConnection();

    $reservationRepository = new ReservationRepository(
        $databaseConnection
    );

    $reservations = $reservationRepository->findAll();

    echo "Reservations retrieved successfully!" . PHP_EOL;
    echo "Total reservations: " . count($reservations) . PHP_EOL;

    print_r($reservations);
} catch (Throwable $exception) {
    echo "Reservation repository test failed: "
        . $exception->getMessage() . PHP_EOL;

    exit(1);
}
