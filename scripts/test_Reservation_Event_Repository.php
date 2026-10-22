<?php

use Sergi\NidoReserva\Database\DatabaseConnection;
use Sergi\NidoReserva\Repository\ReservationEventRepository;

require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    // Create the database connection.
    $databaseConnection = new DatabaseConnection();

    // Create the reservation event repository.
    $reservationEventRepository = new ReservationEventRepository(
        $databaseConnection
    );

    // Use the ID of an existing reservation.
    $reservationId = '123e4567-e89b-12d3-a456-426614174001';

    // Insert a test event associated with the reservation.
    $eventCreated = $reservationEventRepository->create(
        $reservationId,
        'prueba',
        'Test event created by the repository test script.'
    );

    // Display the result of the insertion.
    if ($eventCreated) {
        echo "Reservation event created successfully!" . PHP_EOL;
    } else {
        echo "Failed to create reservation event." . PHP_EOL;
        exit(1);
    }
} catch (Throwable $exception) {
    // Display the error if the test fails.
    echo "Reservation event repository test failed: "
        . $exception->getMessage() . PHP_EOL;

    exit(1);
}