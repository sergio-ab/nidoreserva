<?php

namespace Sergi\Misterplan\Repository;

use Sergi\Misterplan\Database\DatabaseConnection;
use PDO;

class ReservationRepository
{
    private PDO $connection;

    public function __construct(DatabaseConnection $databaseConnection)
    {
        $this->connection = $databaseConnection->getConnection();
    }

    public function findAll(): array
    {
        $query = 'SELECT * FROM RESERVA ORDER BY FECHA_CREACION DESC';

        $statement = $this->connection->query($query);

        return $statement->fetchAll();
    }

}
