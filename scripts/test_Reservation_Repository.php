<?php

// Import the classes needed to connect to the database
// and access reservation data through the repository.
use Sergi\Misterplan\Database\DatabaseConnection;
use Sergi\Misterplan\Repository\ReservationRepository;

// Load Composer's autoloader to make the project's classes
// and installed dependencies available.
require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    // Create a database connection using the configuration from .env.
    $databaseConnection = new DatabaseConnection();

    // Create the reservation repository and pass it the database connection.
    // The repository handles queries to the RESERVA table.
    $reservationRepository = new ReservationRepository(
        $databaseConnection
    );

    // Retrieve all reservations without applying any filters.
    $reservations = $reservationRepository->findAll();

    // Retrieve only reservations whose status is "pendiente".
    $pendingReservations = $reservationRepository->findAll([
        'estado' => 'pendiente',
    ]);

    // Display the number of pending reservations found.
    echo PHP_EOL . "Pending reservations: "
        . count($pendingReservations) . PHP_EOL;

    // Find reservations whose guest name contains "Maria".
    // This can match names such as "Maria López".
    $nameFilteredReservations = $reservationRepository->findAll([
        'nombre_huesped' => 'Maria',
    ]);

    // Display the number of reservations matching the name filter.
    echo PHP_EOL . "Reservations matching name 'Maria': "
        . count($nameFilteredReservations) . PHP_EOL;

    // Find reservations with a check-in date between January 2 and 3, 2026.
    // Both the start date and the end date are included.
    $dateFilteredReservations = $reservationRepository->findAll([
        'fecha_entrada_desde' => '2026-01-02',
        'fecha_entrada_hasta' => '2026-01-03',
    ]);

    // Display the number of reservations matching the date range.
    echo PHP_EOL . "Reservations matching check-in date range: "
        . count($dateFilteredReservations) . PHP_EOL;

    // Find confirmed reservations whose guest name contains "Maria".
    $combinedFilteredReservations = $reservationRepository->findAll([
        'estado' => 'confirmada',
        'nombre_huesped' => 'Maria',
        ]);

    // Display the number of reservations matching both filters.
    echo PHP_EOL . "Reservations matching combined filters: "
        . count($combinedFilteredReservations) . PHP_EOL;

    // Confirm that the reservation queries completed without throwing an exception.
    echo "Reservations retrieved successfully!" . PHP_EOL;

    // Display the total number of reservations retrieved without filters.
    echo "Total reservations: " . count($reservations) . PHP_EOL;

    // Print all reservation records to inspect the returned data.
    print_r($reservations);

} catch (Throwable $exception) {
    // If an error occurs during the connection or a query,
    // display an error message with details from the exception.
    echo "Reservation repository test failed: "
        . $exception->getMessage() . PHP_EOL;

    // Exit with a non-zero status to indicate that the test failed.
    exit(1);
}