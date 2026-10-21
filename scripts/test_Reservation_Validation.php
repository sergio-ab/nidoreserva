<?php

use Sergi\Misterplan\Database\DatabaseConnection;
use Sergi\Misterplan\Service\ReservationService;

require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Verify that invalid reservation data is rejected.
 *
 * @param ReservationService $service The reservation service to test.
 * @param array $data The reservation data to validate.
 * @param string $testName The name of the test case.
 * @return void
 */
function testInvalidReservationData(
    ReservationService $service,
    array $data,
    string $testName
): void {
    try {
        // Attempt to create a reservation with invalid data.
        $service->create($data);
    } catch (InvalidArgumentException $exception) {
        // Report success when the validation rejects the data.
        echo "[PASS] {$testName}" . PHP_EOL;
        return;
    } catch (Throwable $exception) {
        // Report unexpected errors separately from validation errors.
        throw new RuntimeException(
            "Unexpected error in '{$testName}': "
            . $exception->getMessage(),
            0,
            $exception
        );
    }

    // Fail the test if the invalid data was accepted.
    throw new RuntimeException(
        "The validation did not reject: {$testName}"
    );
}

try {
    // Initialize the database connection and reservation service.
    $databaseConnection = new DatabaseConnection();
    $reservationService = new ReservationService($databaseConnection);

    // Define a valid reservation as the starting point for each test.
    $validData = [
        'nombre_huesped' => 'Test Guest',
        'email_huesped' => 'test.guest@example.com',
        'nombre_alojamiento' => 'Test Hotel',
        'fecha_entrada' => '2026-11-10',
        'fecha_salida' => '2026-11-13',
        'importe' => 250.00,
        'notas' => 'Validation test.',
    ];

    // Test that the guest name is required.
    $data = $validData;
    $data['nombre_huesped'] = '';
    testInvalidReservationData(
        $reservationService,
        $data,
        'Empty guest name'
    );

    // Test that the guest email must be valid.
    $data = $validData;
    $data['email_huesped'] = 'invalid-email';
    testInvalidReservationData(
        $reservationService,
        $data,
        'Invalid guest email'
    );

    // Test that impossible calendar dates are rejected.
    $data = $validData;
    $data['fecha_entrada'] = '2026-02-30';
    testInvalidReservationData(
        $reservationService,
        $data,
        'Impossible check-in date'
    );

    // Test that check-out must be later than check-in.
    $data = $validData;
    $data['fecha_salida'] = '2026-11-10';
    testInvalidReservationData(
        $reservationService,
        $data,
        'Check-out equal to check-in'
    );

    // Test that negative reservation amounts are rejected.
    $data = $validData;
    $data['importe'] = -10;
    testInvalidReservationData(
        $reservationService,
        $data,
        'Negative reservation amount'
    );

    // Test that non-numeric reservation amounts are rejected.
    $data = $validData;
    $data['importe'] = 'invalid';
    testInvalidReservationData(
        $reservationService,
        $data,
        'Non-numeric reservation amount'
    );

    // Test that a missing required field is rejected.
    $data = $validData;
    unset($data['nombre_alojamiento']);
    testInvalidReservationData(
        $reservationService,
        $data,
        'Missing accommodation name'
    );

    echo PHP_EOL . "All validation tests passed." . PHP_EOL;
} catch (Throwable $exception) {
    // Display the failure and return a non-zero exit code.
    echo "[FAIL] " . $exception->getMessage() . PHP_EOL;
    exit(1);
}