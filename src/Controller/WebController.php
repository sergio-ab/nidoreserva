<?php

namespace Sergi\NidoReserva\Controller;

use Sergi\NidoReserva\Repository\ReservationRepository;
use Sergi\NidoReserva\Repository\ReservationEventRepository;
use Sergi\NidoReserva\Database\DatabaseConnection;
use Sergi\NidoReserva\Service\TemplateRenderer;
use Throwable;

class WebController
{
    private ReservationRepository $reservationRepository;
    private ReservationEventRepository $reservationEventRepository;
    private TemplateRenderer $templateRenderer;

    public function __construct()
    {
        $databaseConnection = new DatabaseConnection();

        $this->reservationRepository = new ReservationRepository(
            $databaseConnection
        );

        $this->reservationEventRepository = new ReservationEventRepository(
            $databaseConnection
        );

        $this->templateRenderer = new TemplateRenderer();
    }

    public function home(): void
    {
        echo $this->templateRenderer->render('home.html.twig');
    }

    public function notFound(): void
    {
        echo $this->templateRenderer->render(
            '404.html.twig',
        );
    }

    public function index(): void
    {
        try {
            $reservations = $this->reservationRepository->findAll();

            echo $this->templateRenderer->render(
                'reservations/index.html.twig',
                ['reservations' => $reservations]
            );
        } catch (Throwable $exception) {
            http_response_code(500);

            echo $this->templateRenderer->render(
                'error.html.twig',
                ['message' => 'No se pudieron cargar las reservas.']
            );
        }
    }

    /* Display the reservation creation form. */
    public function new(): void
    {
        echo $this->templateRenderer->render(
            'reservations/new.html.twig'
        );
    }

    /* Display the details of a reservation. */
    public function show(string $id): void
    {
        try {
            $reservation = $this->reservationRepository->findById($id);

            if ($reservation === null) {
                http_response_code(404);

                echo $this->templateRenderer->render(
                    'error.html.twig',
                    ['message' => 'No se ha encontrado la reserva.']
                );

                return;
            }

            $events = $this->reservationEventRepository
                ->findByReservationId($id);

            echo $this->templateRenderer->render(
                'reservations/show.html.twig',
                [
                    'reservation' => $reservation,
                    'events' => $events,
                ]
            );
        } catch (Throwable $exception) {
            http_response_code(500);

            echo $this->templateRenderer->render(
                'error.html.twig',
                ['message' => 'No se pudo cargar el detalle de la reserva.']
            );
        }
    }

}
