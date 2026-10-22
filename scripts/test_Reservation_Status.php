
<?php

use Sergi\NidoReserva\Database\DatabaseConnection;
use Sergi\NidoReserva\Service\ReservationService;

require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    // Initialize the database connection and reservation service.
    $databaseConnection = new DatabaseConnection();
    $reservationService = new ReservationService($databaseConnection);

    // Define the data for a dedicated status test reservation.
    $reservationData = [
        'nombre_huesped' => 'Status Test Guest',
        'email_huesped' => 'status.test@example.com',
        'nombre_alojamiento' => 'Status Test Hotel',
        'fecha_entrada' => '2026-12-10',
        'fecha_salida' => '2026-12-13',
        'importe' => 300.00,
        'notas' => 'Reservation created by the status test script.',
    ];

    // Create a reservation with its initial event.
    $reservation = $reservationService->create($reservationData);
    $reservationId = $reservation['ID'];

    echo "Reservation created: {$reservationId}" . PHP_EOL;
    echo "Initial status: {$reservation['ESTADO']}" . PHP_EOL;

    // Confirm the newly created reservation.
    $updatedReservation = $reservationService->changeStatus(
        $reservationId,
        'confirmada'
    );

    // Verify that the reservation status was updated.
    if ($updatedReservation['ESTADO'] !== 'confirmada') {
        throw new RuntimeException(
            'The reservation status was not updated correctly.'
        );
    }

    echo "[PASS] Reservation status changed to confirmada." . PHP_EOL;

    // Retrieve the events recorded for this reservation.
    $query = '
        SELECT TIPO, DESCRIPCION
        FROM RESERVA_EVENTO
        WHERE RESERVA_ID = :reserva_id
        ORDER BY ID ASC
    ';

    $statement = $databaseConnection->getConnection()->prepare($query);
    $statement->execute([
        'reserva_id' => $reservationId,
    ]);

    $events = $statement->fetchAll();

    // Verify that the confirmation event was recorded.
    $confirmationEventFound = false;

    foreach ($events as $event) {
        if ($event['TIPO'] === 'confirmacion') {
            $confirmationEventFound = true;
            echo "Confirmation event: {$event['DESCRIPCION']}" . PHP_EOL;
        }
    }

    if (!$confirmationEventFound) {
        throw new RuntimeException(
            'The confirmation event was not recorded.'
        );
    }

    
    
    echo "[PASS] Confirmation event recorded." . PHP_EOL;

    // Cancel the previously confirmed reservation.
    $cancelledReservation = $reservationService->changeStatus(
        $reservationId,
        'cancelada'
    );

    // Verify that the reservation status was updated.
    if ($cancelledReservation['ESTADO'] !== 'cancelada') {
        throw new RuntimeException(
            'The reservation was not cancelled correctly.'
        );
    }

    echo "[PASS] Reservation status changed to cancelada." . PHP_EOL;

    // Verify that the cancellation event was recorded.
    $statement->execute([
        'reserva_id' => $reservationId,
    ]);

    $events = $statement->fetchAll();
    $cancellationEventFound = false;

    foreach ($events as $event) {
        if ($event['TIPO'] === 'cancelacion') {
            $cancellationEventFound = true;
            echo "Cancellation event: {$event['DESCRIPCION']}" . PHP_EOL;
        }
    }

    if (!$cancellationEventFound) {
        throw new RuntimeException(
            'The cancellation event was not recorded.'
        );
    }

    echo "[PASS] Cancellation event recorded." . PHP_EOL;
    echo PHP_EOL . "All reservation status tests passed." . PHP_EOL;

} catch (Throwable $exception) {
    // Display the failure and return a non-zero exit code.
    fwrite(STDERR, "[FAIL] " . $exception->getMessage() . PHP_EOL);
    exit(1);
}
