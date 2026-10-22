<?php

namespace Sergi\NidoReserva\Repository;

// Import the database connection class.
use Sergi\NidoReserva\Database\DatabaseConnection;

// Import PDO to interact with the database.
use PDO;

class ReservationRepository
{
    // Store the PDO connection used to execute database queries.
    private PDO $connection;

    // Receive the database connection and obtain the PDO instance.
    public function __construct(DatabaseConnection $databaseConnection)
    {
        $this->connection = $databaseConnection->getConnection();
    }

    /**
     * Retrieve all reservations, optionally applying filters.
     *
     * Supported filters:
     * - estado: reservation status.
     * - fecha_entrada_desde: earliest check-in date.
     * - fecha_entrada_hasta: latest check-in date.
     * - nombre_huesped: guest name to search for.
     *
     * @param array $filters Optional filters to apply to the query.
     * @return array A list of matching reservations.
     */
    public function findAll(array $filters = []): array
    {
        // Start with a query that retrieves all reservations.
        $query = 'SELECT * FROM RESERVA';

        // Store the SQL conditions that will be applied to the query.
        $conditions = [];

        // Store the values used by the prepared statement.
        $parameters = [];

        // Filter reservations by status when a status is provided.
        if (!empty($filters['estado'])) {
            $conditions[] = 'ESTADO = :estado';
            $parameters['estado'] = $filters['estado'];
        }

        // Filter reservations whose check-in date is on or after the start date.
        if (!empty($filters['fecha_entrada_desde'])) {
            $conditions[] = 'FECHA_ENTRADA >= :fecha_entrada_desde';
            $parameters['fecha_entrada_desde'] = $filters['fecha_entrada_desde'];
        }

        // Filter reservations whose check-in date is on or before the end date.
        if (!empty($filters['fecha_entrada_hasta'])) {
            $conditions[] = 'FECHA_ENTRADA <= :fecha_entrada_hasta';
            $parameters['fecha_entrada_hasta'] = $filters['fecha_entrada_hasta'];
        }

        // Filter reservations by guest name using a partial match.
        if (!empty($filters['nombre_huesped'])) {
            $conditions[] = 'NOMBRE_HUESPED LIKE :nombre_huesped';
            $parameters['nombre_huesped'] = '%' . $filters['nombre_huesped'] . '%';
        }

        // Add the conditions to the query, combining them with AND.
        // A reservation must satisfy every applied filter.
        if ($conditions !== []) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        // Display the newest reservations first.
        $query .= ' ORDER BY FECHA_CREACION DESC';

        // Prepare the SQL query to safely handle parameter values.
        $statement = $this->connection->prepare($query);

        // Execute the query using the provided filter values.
        $statement->execute($parameters);

        // Return all matching reservations as associative arrays.
        return $statement->fetchAll();
    }

    /**
     * Find a reservation by its unique identifier.
     *
     * @param string $id The reservation UUID.
     * @return array|null The reservation data, or null if not found.
     */
    public function findById(string $id): ?array
    {
        // Prepare a query to retrieve one reservation by its ID.
        $query = 'SELECT * FROM RESERVA WHERE ID = :id';

        // Prepare the SQL statement to safely handle the ID parameter.
        $statement = $this->connection->prepare($query);

        // Execute the query with the reservation ID.
        $statement->execute([
            'id' => $id,
        ]);

        // Fetch the matching reservation as an associative array.
        $reservation = $statement->fetch();

        // Return the reservation if found, or null otherwise.
        return $reservation ?: null;
    }

    /**
     * Insert a new reservation into the database.
     *
     * @param array $data The reservation data to insert.
     * @return bool Whether the reservation was inserted successfully.
     */
    public function create(array $data): bool
    {
        // Prepare the SQL query for inserting a new reservation.
        $query = '
            INSERT INTO RESERVA (
                ID,
                NOMBRE_HUESPED,
                EMAIL_HUESPED,
                NOMBRE_ALOJAMIENTO,
                FECHA_ENTRADA,
                FECHA_SALIDA,
                ESTADO,
                IMPORTE,
                NOTAS
            ) VALUES (
                :id,
                :nombre_huesped,
                :email_huesped,
                :nombre_alojamiento,
                :fecha_entrada,
                :fecha_salida,
                :estado,
                :importe,
                :notas
            )
        ';

        // Prepare the statement to safely handle the supplied values.
        $statement = $this->connection->prepare($query);

        // Execute the insert using the reservation data.
        return $statement->execute([
            'id' => $data['id'],
            'nombre_huesped' => $data['nombre_huesped'],
            'email_huesped' => $data['email_huesped'],
            'nombre_alojamiento' => $data['nombre_alojamiento'],
            'fecha_entrada' => $data['fecha_entrada'],
            'fecha_salida' => $data['fecha_salida'],
            'estado' => $data['estado'],
            'importe' => $data['importe'],
            'notas' => $data['notas'] ?? null,
        ]);
    }

    /**
     * Update the status of an existing reservation.
     *
     * @param string $id The reservation UUID.
     * @param string $status The new reservation status.
     * @return bool Whether the update was executed successfully.
     */
    public function updateStatus(string $id, string $status): bool
    {
        // Prepare a query to update the reservation status.
        $query = '
            UPDATE RESERVA
            SET ESTADO = :estado
            WHERE ID = :id
        ';

        // Prepare the statement to safely handle the supplied values.
        $statement = $this->connection->prepare($query);

        // Execute the update using the reservation ID and new status.
        return $statement->execute([
            'estado' => $status,
            'id' => $id,
        ]);
    }

}