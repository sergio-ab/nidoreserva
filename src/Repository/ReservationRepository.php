<?php

namespace Sergi\Misterplan\Repository;

// Import the database connection class.
use Sergi\Misterplan\Database\DatabaseConnection;

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
}