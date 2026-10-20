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
}
