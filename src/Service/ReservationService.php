<?php

namespace Sergi\Misterplan\Service;

use Sergi\Misterplan\Database\DatabaseConnection;
use Sergi\Misterplan\Repository\ReservationRepository;
use Sergi\Misterplan\Repository\ReservationEventRepository;
use PDO;

class ReservationService
{
    // Store the PDO connection used to manage database transactions.
    private PDO $connection;

    // Store the repository responsible for reservation data access.
    private ReservationRepository $reservationRepository;

    // Store the repository responsible for reservation event data access.
    private ReservationEventRepository $reservationEventRepository;

    // Initialize the service with the database connection and repositories.
    public function __construct(DatabaseConnection $databaseConnection)
    {
        // Retrieve the PDO connection from the database connection manager.
        $this->connection = $databaseConnection->getConnection();

        // Initialize the repository used to create and retrieve reservations.
        $this->reservationRepository = new ReservationRepository(
            $databaseConnection
        );

        // Initialize the repository used to create reservation events.
        $this->reservationEventRepository = new ReservationEventRepository(
            $databaseConnection
        );
    }
     
    /**
     * Create a reservation and record its initial event in a single transaction.
     *
     * @param array $data The reservation data to be saved.
     * @return array The newly created reservation.
     */
    public function create(array $data): array
    {
        // Validate the reservation data before starting a transaction.
        $this->validateReservationData($data);

        // Start a transaction so both database operations succeed or fail together.
        $this->connection->beginTransaction();

        try {
            // Generate a UUID for the new reservation.
            $reservationId = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                random_int(0, 0xffff),
                random_int(0, 0xffff),
                random_int(0, 0xffff),
                random_int(0, 0x0fff) | 0x4000,
                random_int(0, 0x3fff) | 0x8000,
                random_int(0, 0xffff),
                random_int(0, 0xffff),
                random_int(0, 0xffff)
            );

            // Add the generated ID and default status to the reservation data.
            $data['id'] = $reservationId;
            $data['estado'] = 'pendiente';

            // Create the reservation in the database.
            $this->reservationRepository->create($data);

            // Record the initial event for the new reservation.
            $this->reservationEventRepository->create(
                $reservationId,
                'creacion',
                'Reserva creada con estado pendiente'
            );

            
            // Retrieve the saved reservation before committing the transaction.
            $reservation = $this->reservationRepository->findById($reservationId);

            // Throw an exception if the newly created reservation cannot be retrieved.
            if ($reservation === null) {
                throw new \RuntimeException(
                    'The reservation was created but could not be retrieved.'
                );
            }

            // Commit the transaction after all operations succeed.
            $this->connection->commit();

            // Return the newly created reservation.
            return $reservation;

        } catch (\Throwable $exception) {
            // Roll back the transaction if any operation fails.
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            // Re-throw the exception so the caller can handle the error.
            throw $exception;
        }
    }

    /**
     * Validate the data required to create a reservation.
     *
     * @param array $data The reservation data to validate.
     * @return void
     */
    private function validateReservationData(array $data): void
    {
        // Define the required reservation fields.
        $requiredFields = [
            'nombre_huesped',
            'email_huesped',
            'nombre_alojamiento',
            'fecha_entrada',
            'fecha_salida',
            'importe',
        ];

        // Check that every required field is present and not empty.
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                throw new \InvalidArgumentException(
                    "The field '{$field}' is required."
                );
            }
        }

        // Validate the guest email address.
        if (!filter_var($data['email_huesped'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                'The guest email address is invalid.'
            );
        }

        // Validate the reservation dates and their order.
        $checkIn = \DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $data['fecha_entrada']
        );
        $checkOut = \DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $data['fecha_salida']
        );

        if (
            !$checkIn
            || !$checkOut
            || $checkIn->format('Y-m-d') !== $data['fecha_entrada']
            || $checkOut->format('Y-m-d') !== $data['fecha_salida']
            || $checkOut <= $checkIn
        ) {
            throw new \InvalidArgumentException(
                'The reservation dates are invalid.'
            );
        }

        // Validate that the reservation amount is numeric and non-negative.
        if (!is_numeric($data['importe']) || (float) $data['importe'] < 0) {
            throw new \InvalidArgumentException(
                'The reservation amount must be a non-negative number.'
            );
        }
    }
}