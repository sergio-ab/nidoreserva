
<?php

use Sergi\NidoReserva\Database\DatabaseConnection;
use Sergi\NidoReserva\Service\ReservationService;

require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    // Create the database connection.
    $databaseConnection = new DatabaseConnection();

    // Initialize the reservation service.
    $reservationService = new ReservationService($databaseConnection);

    // Define the data for a test reservation.
    $reservationData = [
        'nombre_huesped' => 'Laura Martín',
        'email_huesped' => 'laura.martin@example.com',
        'nombre_alojamiento' => 'Hotel de Prueba',
        'fecha_entrada' => '2026-11-10',
        'fecha_salida' => '2026-11-13',
        'importe' => 275.50,
        'notas' => 'Test reservation created by the service test script.',
    ];

    // Create the reservation and its initial event.
    $reservation = $reservationService->create($reservationData);

    // Display the created reservation.
    echo "Reservation created successfully!" . PHP_EOL;
    echo "Reservation ID: " . $reservation['ID'] . PHP_EOL;
    echo "Guest name: " . $reservation['NOMBRE_HUESPED'] . PHP_EOL;
    echo "Status: " . $reservation['ESTADO'] . PHP_EOL;
} catch (Throwable $exception) {
    // Display the error if the test fails.
    echo "Reservation service test failed: "
        . $exception->getMessage() . PHP_EOL;

    exit(1);
}
