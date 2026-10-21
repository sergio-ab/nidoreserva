<?php

use Sergi\Misterplan\Controller\ReservationController;
use Sergi\Misterplan\Router\Router;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Set the default response content type to JSON.
header('Content-Type: application/json; charset=utf-8');

try {
    // Initialize the router and reservation controller.
    $router = new Router();
    $reservationController = new ReservationController();

    // Register the reservation routes.
    $router->add(
        'GET',
        '/api/reservations',
        [$reservationController, 'index']
    );

    $router->add(
        'GET',
        '/api/reservations/{id}',
        [$reservationController, 'show']
    );

    // Register the reservation creation route.
    $router->add(
        'POST',
        '/api/reservations',
        [$reservationController, 'store']
    );

    // Register the reservation status update route.
    $router->add(
        'PATCH',
        '/api/reservations/{id}/status',
        [$reservationController, 'updateStatus']
    );

    // Dispatch the current HTTP request.
    $router->dispatch(
        $_SERVER['REQUEST_METHOD'],
        $_SERVER['REQUEST_URI']
    );
} catch (Throwable $exception) {
    // Return a generic error without exposing internal details.
    http_response_code(500);

    echo json_encode([
        'error' => 'An unexpected server error occurred.',
    ]);
}
