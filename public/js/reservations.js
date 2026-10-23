'use strict';

// Get the elements used by the reservation list.
const filterForm = document.getElementById('reservation-filters');
const tableBody = document.getElementById('reservations-table-body');
const messageElement = document.getElementById('reservation-message');

// Get the reservation creation form elements.
const createForm = document.getElementById('reservation-create-form');
const formMessageElement = document.getElementById('form-message');

/* Get the reservation status action elements. */
const statusMessageElement = document.getElementById('status-message');
const confirmButton = document.getElementById('confirm-reservation');
const cancelButton = document.getElementById('cancel-reservation');

// Get the reservation date inputs.
const checkInInput = document.getElementById('check-in');
const checkOutInput = document.getElementById('check-out');

// Return today's date in the local YYYY-MM-DD format.
function getTodayDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

// Prevent selecting dates before today.
if (checkInInput && checkOutInput) {
    const todayDate = getTodayDate();

    checkInInput.min = todayDate;
    checkOutInput.min = todayDate;

    // Keep the checkout date after the check-in date.
    checkInInput.addEventListener('change', () => {
        checkOutInput.min = checkInInput.value || todayDate;

        if (
            checkOutInput.value
            && checkOutInput.value < checkOutInput.min
        ) {
            checkOutInput.value = '';
        }
    });
}

/* Read the current values from the reservation filter form. */
function getFilterValues() {
    const formData = new FormData(filterForm);

    return {
        nombre_huesped: formData.get('nombre_huesped') || '',
        estado: formData.get('estado') || '',
        fecha_entrada_desde: formData.get('fecha_entrada_desde') || '',
        fecha_entrada_hasta: formData.get('fecha_entrada_hasta') || '',
    };
}

// Store the API endpoint.
const reservationsApiUrl = '/api/reservations';

// Display a message to the user.
function showMessage(message) {
    messageElement.textContent = message;
}

/* Request reservations from the API. */
async function fetchReservations(filters = {}) {
    const queryParams = new URLSearchParams();

    Object.entries(filters).forEach(([key, value]) => {
        if (value.trim() !== '') {
            queryParams.append(key, value);
        }
    });

    const queryString = queryParams.toString();
    const url = queryString
        ? `${reservationsApiUrl}?${queryString}`
        : reservationsApiUrl;

    const response = await fetch(url, {
        headers: {
            Accept: 'application/json',
        },
    });

    if (!response.ok) {
        throw new Error('No se pudieron cargar las reservas.');
    }

    const result = await response.json();
    const reservations = result.data;

    if (!Array.isArray(reservations)) {
        throw new Error('La respuesta de la API no tiene el formato esperado.');
    }

    return reservations;

}

/* Load and display reservations from the API. */
async function loadReservations() {
    showMessage('Cargando reservas...');

    try {
        const reservations = await fetchReservations();
        renderReservations(reservations);
    } catch (error) {
        console.error('Reservations API error:', error);
        showMessage('No se pudieron cargar las reservas.');
    }
}

/* Format a date for display in the reservation table. */
function formatDate(dateString) {
    const date = new Date(`${dateString}T00:00:00`);

    return new Intl.DateTimeFormat('es-ES').format(date);
}

/* Format an amount as euros. */
function formatAmount(amount) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
    }).format(Number(amount));
}

/* Create a table cell using text content. */
function createCell(value) {
    const cell = document.createElement('td');
    cell.textContent = value;

    return cell;
}

/* Render the reservations returned by the API. */
function renderReservations(reservations) {
    tableBody.replaceChildren();

    if (reservations.length === 0) {
        const row = document.createElement('tr');
        const cell = createCell('No hay reservas que mostrar.');

        cell.colSpan = 7;
        row.appendChild(cell);
        tableBody.appendChild(row);

        showMessage('No se encontraron reservas.');
        return;
    }

    reservations.forEach((reservation) => {
        const row = document.createElement('tr');

        row.appendChild(createCell(reservation.NOMBRE_HUESPED));
        row.appendChild(createCell(reservation.NOMBRE_ALOJAMIENTO));
        row.appendChild(createCell(formatDate(reservation.FECHA_ENTRADA)));
        row.appendChild(createCell(formatDate(reservation.FECHA_SALIDA)));

        const statusCell = document.createElement('td');
        const statusBadge = document.createElement('span');

        statusBadge.classList.add(
            'status',
            `status-${reservation.ESTADO}`
        );
        statusBadge.textContent =
            reservation.ESTADO.charAt(0).toUpperCase()
            + reservation.ESTADO.slice(1);

        statusCell.appendChild(statusBadge);
        row.appendChild(statusCell);

        row.appendChild(createCell(formatAmount(reservation.IMPORTE)));

        const actionsCell = document.createElement('td');
        const detailLink = document.createElement('a');

        detailLink.href = `/reservations/${encodeURIComponent(reservation.ID)}`;
        detailLink.textContent = 'Ver detalle';

        actionsCell.appendChild(detailLink);
        row.appendChild(actionsCell);

        tableBody.appendChild(row);
    });

    showMessage(`Se han encontrado ${reservations.length} reservas.`);
}

if (filterForm && tableBody && messageElement) {
    loadReservations();

/* Apply reservation filters without reloading the page. */
filterForm.addEventListener('submit', async (event) => {
    event.preventDefault();

    showMessage('Buscando reservas...');

    try {
        const filters = getFilterValues();
        const reservations = await fetchReservations(filters);

        renderReservations(reservations);
    } catch (error) {
        console.error('Reservation filter error:', error);
        showMessage('No se pudieron filtrar las reservas.');
    }
});

/* Reload all reservations after resetting the filter form. */
filterForm.addEventListener('reset', () => {
    loadReservations();
});
}

/* Handle reservation creation form submission. */
if (createForm && formMessageElement) {
    createForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(createForm);

        const reservationData = {
            nombre_huesped: formData.get('nombre_huesped').trim(),
            email_huesped: formData.get('email_huesped').trim(),
            nombre_alojamiento: formData.get('nombre_alojamiento').trim(),
            fecha_entrada: formData.get('fecha_entrada'),
            fecha_salida: formData.get('fecha_salida'),
            importe: formData.get('importe'),
            notas: formData.get('notas').trim(),
        };

        formMessageElement.textContent = 'Enviando reserva al servidor...';

        try {
            const response = await fetch('/api/reservations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(reservationData),
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(
                    result.error || 'No se pudo crear la reserva.'
                );
            }

            formMessageElement.textContent = 'Reserva creada correctamente.';
            createForm.reset();
        } catch (error) {
            console.error('Reservation creation error:', error);

            formMessageElement.textContent =
                error.message || 'No se pudo conectar con el servidor.';
        }
    });
}

/* Change a reservation status through the API. */
async function changeReservationStatus(newStatus) {
    if (!statusMessageElement) {
        return;
    }

    const statusLabels = {
        confirmada: 'confirmar',
        cancelada: 'cancelar',
    };

    const statusMessages = {
        confirmada: 'Reserva confirmada correctamente.',
        cancelada: 'Reserva cancelada correctamente.',
    };

    if (newStatus === 'cancelada') {
        const confirmed = window.confirm(
            '¿Seguro que quieres cancelar esta reserva?'
        );

        if (!confirmed) {
            return;
        }
    }

    statusMessageElement.textContent = 'Actualizando el estado...';

    if (confirmButton) {
        confirmButton.disabled = true;
    }

    if (cancelButton) {
        cancelButton.disabled = true;
    }

    try {
        const pathParts = window.location.pathname.split('/');
        const reservationId = decodeURIComponent(pathParts[pathParts.length - 1]);

        if (!reservationId) {
            throw new Error('No se ha podido identificar la reserva.');
        }

        const response = await fetch(
            `${reservationsApiUrl}/${encodeURIComponent(reservationId)}/status`,
            {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    estado: newStatus,
                }),
            }
        );

        const result = await response.json();

        if (!response.ok) {
            throw new Error(
                result.error || 'No se pudo actualizar el estado.'
            );
        }

        statusMessageElement.textContent = statusMessages[newStatus];

        window.location.reload();
    } catch (error) {
        console.error('Reservation status update error:', error);

        statusMessageElement.textContent =
            error.message || 'No se pudo conectar con el servidor.';

        if (confirmButton) {
            confirmButton.disabled = false;
        }

        if (cancelButton) {
            cancelButton.disabled = false;
        }
    }
}

/* Handle reservation status action clicks. */
if (confirmButton) {
    confirmButton.addEventListener('click', () => {
        changeReservationStatus('confirmada');
    });
}

if (cancelButton) {

    cancelButton.addEventListener('click', () => {
        changeReservationStatus('cancelada');
    });
}