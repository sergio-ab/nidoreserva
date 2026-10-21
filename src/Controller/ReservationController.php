<?php

namespace Sergi\Misterplan\Controller;

use Sergi\Misterplan\Database\DatabaseConnection;
use Sergi\Misterplan\Repository\ReservationRepository;

class ReservationController
{
    private ReservationRepository $reservationRepository;

    public function __construct()
    {
        $databaseConnection = new DatabaseConnection();

        $this->reservationRepository = new ReservationRepository(
            $databaseConnection
        );
    }

    /**
     * List reservations using the supported filters.
     */
    public function index(): void
    {
        // Read the supported filters from the query string.
        $filters = [
            'estado' => $_GET['estado'] ?? null,
            'fecha_entrada_desde' => $_GET['fecha_entrada_desde'] ?? null,
            'fecha_entrada_hasta' => $_GET['fecha_entrada_hasta'] ?? null,
            'nombre_huesped' => $_GET['nombre_huesped'] ?? null,
        ];

        // Remove filters that were not provided.
        $filters = array_filter(
            $filters,
            static fn ($value) => $value !== null && $value !== ''
        );

        // Retrieve the matching reservations.
        $reservations = $this->reservationRepository->findAll($filters);

        // Return the reservations as JSON.
        http_response_code(200);

        echo json_encode(
            [
                'data' => $reservations,
            ],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }


    /**
     * Show a reservation by its ID.
     */
    public function show(string $id): void
    {
        // Find the reservation by its ID.
        $reservation = $this->reservationRepository->findById($id);

        // Return a 404 response when the reservation does not exist.
        if ($reservation === null) {
            http_response_code(404);

            echo json_encode([
                'error' => 'Reservation not found.',
            ]);

            return;
        }

        // Return the reservation as JSON.
        http_response_code(200);

        echo json_encode(
            [
                'data' => $reservation,
            ],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }


    /**
     * Create a reservation from a JSON request.
     */
    public function store(): void
    {
        // Read the JSON request body.
        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody, true);

        // Reject invalid JSON or a JSON value that is not an object.
        if (
            json_last_error() !== JSON_ERROR_NONE
            || !is_array($data)
            || array_is_list($data)
        ) {
            http_response_code(400);

            echo json_encode([
                'error' => 'A valid JSON object is required.',
            ]);

            return;
        }

        try {
            // Create the reservation using the business service.
            $databaseConnection = new DatabaseConnection();
            $reservationService = new \Sergi\Misterplan\Service\ReservationService(
                $databaseConnection
            );

            $reservation = $reservationService->create($data);

            // Return the created reservation.
            http_response_code(201);

            echo json_encode(
                [
                    'data' => $reservation,
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        } catch (\InvalidArgumentException $exception) {
            // Return validation errors to the client.
            http_response_code(400);

            echo json_encode([
                'error' => $exception->getMessage(),
            ]);
        } catch (\Throwable $exception) {
            // Avoid exposing internal server details.
            http_response_code(500);

            echo json_encode([
                'error' => 'An unexpected server error occurred.',
            ]);
        }
    }


    /**
     * Change a reservation status from a JSON request.
     */
    public function updateStatus(string $id): void
    {
        // Read and decode the JSON request body.
        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody, true);

        // Reject invalid JSON or a JSON value that is not an object.
        if (
            json_last_error() !== JSON_ERROR_NONE
            || !is_array($data)
            || array_is_list($data)
        ) {
            http_response_code(400);

            echo json_encode([
                'error' => 'A valid JSON object is required.',
            ]);

            return;
        }

        // Only confirmation and cancellation are allowed through this endpoint.
        $newStatus = $data['estado'] ?? null;

        if (
            !is_string($newStatus)
            || !in_array($newStatus, ['confirmada', 'cancelada'], true)
        ) {
            http_response_code(400);

            echo json_encode([
                'error' => "The field 'estado' must be 'confirmada' or 'cancelada'.",
            ]);

            return;
        }

        try {
            // Check that the reservation exists before changing its status.
            if ($this->reservationRepository->findById($id) === null) {
                http_response_code(404);

                echo json_encode([
                    'error' => 'Reservation not found.',
                ]);

                return;
            }

            // Apply the status change and record its event.
            $databaseConnection = new DatabaseConnection();
            $reservationService = new \Sergi\Misterplan\Service\ReservationService(
                $databaseConnection
            );

            $reservation = $reservationService->changeStatus($id, $newStatus);

            // Return the updated reservation.
            http_response_code(200);

            echo json_encode(
                [
                    'data' => $reservation,
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        } catch (\InvalidArgumentException $exception) {
            // Return invalid status transitions as client errors.
            http_response_code(400);

            echo json_encode([
                'error' => $exception->getMessage(),
            ]);
        } catch (\Throwable $exception) {
            // Avoid exposing internal server details.
            http_response_code(500);

            echo json_encode([
                'error' => 'An unexpected server error occurred.',
            ]);
        }
    }

}
