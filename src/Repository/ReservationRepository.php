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

    
    public function findAll(array $filters = []): array
    {
        $query = 'SELECT * FROM RESERVA';
        $conditions = [];
        $parameters = [];

        if (!empty($filters['estado'])) {
            $conditions[] = 'ESTADO = :estado';
            $parameters['estado'] = $filters['estado'];
        }

        if (!empty($filters['fecha_entrada_desde'])) {
            $conditions[] = 'FECHA_ENTRADA >= :fecha_entrada_desde';
            $parameters['fecha_entrada_desde'] = $filters['fecha_entrada_desde'];
        }

        if (!empty($filters['fecha_entrada_hasta'])) {
            $conditions[] = 'FECHA_ENTRADA <= :fecha_entrada_hasta';
            $parameters['fecha_entrada_hasta'] = $filters['fecha_entrada_hasta'];
        }

        if (!empty($filters['nombre_huesped'])) {
            $conditions[] = 'NOMBRE_HUESPED LIKE :nombre_huesped';
            $parameters['nombre_huesped'] = '%' . $filters['nombre_huesped'] . '%';
        }

        if ($conditions !== []) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $query .= ' ORDER BY FECHA_CREACION DESC';

        $statement = $this->connection->prepare($query);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

}
