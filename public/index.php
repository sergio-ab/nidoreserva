<?php

use Sergi\NidoReserva\Controller\ReservationController;
use Sergi\NidoReserva\Controller\WebController;
use Sergi\NidoReserva\Router\Router;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Set the response content type according to the requested path.
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($requestPath !== null && str_starts_with($requestPath, '/api/')) {
    header('Content-Type: application/json; charset=utf-8');
} else {
    header('Content-Type: text/html; charset=utf-8');
}

try {
    // Initialize the router and reservation controller.
    $router = new Router();
    $reservationController = new ReservationController();

    // Initialize the web controller.
    $webController = new WebController();

    // Register the web interface route.
    $router->add(
        'GET',
        '/',
        [$webController, 'index']
    );

    // Register the reservation creation page.
    $router->add(
        'GET',
        '/reservations/new',
        [$webController, 'new']
    );

    // Register the reservation detail page.
    $router->add(
        'GET',
        '/reservations/{id}',
        [$webController, 'show']
    );

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
