-- --------------------------------------------------------
-- CREACION DE LA TABLA RESERVA
-- --------------------------------------------------------

CREATE TABLE RESERVA (
    ID CHAR(36) NOT NULL, 
    NOMBRE_HUESPED VARCHAR(255) NOT NULL,
    EMAIL_HUESPED VARCHAR(255) NOT NULL,
    NOMBRE_ALOJAMIENTO VARCHAR(255) NOT NULL,
    FECHA_ENTRADA DATE NOT NULL,
    FECHA_SALIDA DATE NOT NULL,
    ESTADO ENUM('pendiente', 'confirmada', 'cancelada') DEFAULT 'pendiente' NOT NULL,
    IMPORTE DECIMAL(10, 2) NOT NULL,
    NOTAS TEXT, 
    FECHA_CREACION DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT PK_RESERVA PRIMARY KEY (ID),
    CONSTRAINT CK_RESERVA_FECHAS CHECK (FECHA_ENTRADA < FECHA_SALIDA), 
    CONSTRAINT CK_RESERVA_IMPORTE CHECK (IMPORTE >= 0)
);

-- --------------------------------------------------------
-- CREACION DE LA TABLA RESERVA_EVENTO
-- --------------------------------------------------------

CREATE TABLE RESERVA_EVENTO (
    ID INT AUTO_INCREMENT NOT NULL,
    RESERVA_ID CHAR(36) NOT NULL,
    TIPO VARCHAR(100) NOT NULL,
    DESCRIPCION VARCHAR(255) NOT NULL,
    FECHA_CREACION DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT PK_RESERVA_EVENTO PRIMARY KEY (ID),
    CONSTRAINT FK_RESERVA_EVENTO_RESERVA FOREIGN KEY (RESERVA_ID) REFERENCES RESERVA(ID)
);


-- --------------------------------------------------------
-- DATOS DE PRUEBA PARA LA TABLA RESERVA
-- --------------------------------------------------------

INSERT INTO RESERVA (
    ID,
    NOMBRE_HUESPED,
    EMAIL_HUESPED,
    NOMBRE_ALOJAMIENTO,
    FECHA_ENTRADA,
    FECHA_SALIDA,
    ESTADO,
    IMPORTE,
    NOTAS
) VALUES (
    '123e4567-e89b-12d3-a456-426614174000',
    'Juan Perez',
    'juan.perez@example.com',
    'Cabaña Quintana',
    '2026-01-01',
    '2026-01-03',
    'pendiente',
    100.00,
    NULL
), (
    '123e4567-e89b-12d3-a456-426614174001',
    'Maria López',
    'maria.lopez@example.com',
    'Casa Verde',
    '2026-01-02',
    '2026-01-04',
    'confirmada',
    150.00,
    NULL
), (
    '123e4567-e89b-12d3-a456-426614174002',
    'Pedro Gómez',
    'pedro.gomez@example.com',
    'Casa Azul',
    '2026-01-03',
    '2026-01-05',
    'cancelada',
    200.00,
    NULL
);  

-- --------------------------------------------------------
-- DATOS DE PRUEBA PARA LA TABLA RESERVA_EVENTO
-- --------------------------------------------------------

INSERT INTO RESERVA_EVENTO (
    RESERVA_ID,
    TIPO,
    DESCRIPCION
) VALUES (
    '123e4567-e89b-12d3-a456-426614174000',
    'creación',
    'Reserva creada'
), (
    '123e4567-e89b-12d3-a456-426614174001',
    'confirmación',
    'Reserva confirmada'
), (
    '123e4567-e89b-12d3-a456-426614174002',
    'cancelación',
    'Reserva cancelada'
), (
    '123e4567-e89b-12d3-a456-426614174000',
    'modificación',
    'Reserva modificada'
), (
    '123e4567-e89b-12d3-a456-426614174001',
    'reembolso',
    'Reserva reembolsada'
), (
    '123e4567-e89b-12d3-a456-426614174002',
    'reembolso',
    'Reserva reembolsada'
);
