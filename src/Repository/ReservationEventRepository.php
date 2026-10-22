<?php

namespace Sergi\NidoReserva\Repository;

use Sergi\NidoReserva\Database\DatabaseConnection;
use PDO;

class ReservationEventRepository
{
    // Store the PDO connection used to execute database queries.
    private PDO $connection;

    // Receive the database connection and obtain the PDO instance.
    public function __construct(DatabaseConnection $databaseConnection)
    {
        $this->connection = $databaseConnection->getConnection();
    }

    /**
     * Create an event associated with a reservation.
     *
     * @param string $reservationId The UUID of the reservation.
     * @param string $type The event type.
     * @param string $description A description of the event.
     * @return bool Whether the event was inserted successfully.
     */
    public function create(
        string $reservationId,
        string $type,
        string $description
    ): bool {
        // Prepare a query to insert a new reservation event.
        $query = '
            INSERT INTO RESERVA_EVENTO (
                RESERVA_ID,
                TIPO,
                DESCRIPCION
            ) VALUES (
                :reserva_id,
                :tipo,
                :descripcion
            )
        ';

        // Prepare the statement to safely handle the supplied values.
        $statement = $this->connection->prepare($query);

        // Execute the query with the event data.
        return $statement->execute([
            'reserva_id' => $reservationId,
            'tipo' => $type,
            'descripcion' => $description,
        ]);
    }

    /* Retrieve the events associated with a reservation. */
    public function findByReservationId(string $reservationId): array
    {
        $query = '
            SELECT
                ID,
                RESERVA_ID,
                TIPO,
                DESCRIPCION,
                FECHA_CREACION
            FROM RESERVA_EVENTO
            WHERE RESERVA_ID = :reserva_id
            ORDER BY FECHA_CREACION ASC, ID ASC
        ';

        $statement = $this->connection->prepare($query);

        $statement->execute([
            'reserva_id' => $reservationId,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

}
