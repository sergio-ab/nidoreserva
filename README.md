# NidoReserva

Aplicación web para la gestión de reservas de alojamiento, desarrollada como prueba técnica con PHP nativo, MySQL/MariaDB, Twig y JavaScript, sin utilizar frameworks PHP como Laravel o Symfony.

El proyecto permite consultar, filtrar, crear, confirmar y cancelar reservas, así como consultar el historial de eventos asociado a cada una.

## Índice

- [Funcionalidades](#funcionalidades)
- [Tecnologías](#tecnologías)
- [Requisitos previos](#requisitos-previos)
- [Instalación y configuración local](#instalación-y-configuración-local)
- [Uso de la aplicación](#uso-de-la-aplicación)
- [API REST](#api-rest)
- [Modelo de datos](#modelo-de-datos)
- [Arquitectura](#arquitectura)
- [Validaciones y reglas de negocio](#validaciones-y-reglas-de-negocio)
- [Seguridad](#seguridad)
- [Escalabilidad y mejoras futuras](#escalabilidad-y-mejoras-futuras)
- [Despliegue](#despliegue)
- [Licencia](#licencia)

## Funcionalidades

- Listado de reservas con filtros combinables por estado, intervalo de fechas de entrada y nombre del huésped.
- Consulta del detalle de cada reserva.
- Consulta del historial de eventos de una reserva.
- Creación de reservas mediante un formulario web.
- Validación de los datos en el cliente y en el servidor.
- Confirmación y cancelación de reservas.
- Registro de eventos al crear una reserva o cambiar su estado.
- Interfaz web con filtros y acciones mediante JavaScript, sin recargar la página para esas operaciones.
- API que devuelve respuestas JSON con códigos HTTP adecuados.
- Plantillas Twig con una plantilla base compartida.

## Tecnologías

- **PHP:** lenguaje principal de la aplicación y de la API.
- **MySQL/MariaDB:** almacenamiento de los datos.
- **PDO:** acceso a la base de datos y ejecución de consultas preparadas.
- **Twig:** renderizado de las vistas del servidor.
- **JavaScript:** interacción con la interfaz y consumo de la API.
- **HTML y CSS:** estructura y presentación de la aplicación.
- **Composer:** gestión de dependencias y autoload mediante PSR-4.
- **vlucas/phpdotenv:** lectura de la configuración desde variables de entorno.

El entorno de desarrollo utilizado emplea PHP 8.2.12 y MariaDB 10.4.32. La compatibilidad con otras versiones debe comprobarse en función de las dependencias instaladas y de la configuración del servidor.

## Requisitos previos

Para ejecutar el proyecto localmente se necesita:

- PHP compatible con las dependencias del proyecto.
- Las extensiones de PHP necesarias, especialmente `PDO` y `pdo_mysql`.
- MySQL o MariaDB en ejecución.
- Composer.
- Un navegador web moderno.

En Windows se puede utilizar XAMPP para disponer de PHP, MariaDB/MySQL y phpMyAdmin.

Para comprobar la versión de PHP y que Composer está disponible, ejecuta:

```bash
php -v
composer --version
```

Para comprobar las extensiones de base de datos:

```bash
php -m
```

Verifica que aparezcan `PDO` y `pdo_mysql` en la lista.

## Instalación y configuración local

### 1. Clonar el repositorio

Clona el repositorio y entra en el directorio del proyecto:

```bash
git clone https://github.com/sergio-ab/nidoreserva.git
cd nidoreserva
```

### 2. Instalar las dependencias

Desde la raíz del proyecto, ejecuta:

```bash
composer install
```

Composer instalará las dependencias definidas en `composer.lock` y configurará el autoload de las clases.

No es necesario instalar Twig ni `vlucas/phpdotenv` manualmente.

### 3. Crear la base de datos

Inicia MySQL/MariaDB y crea una base de datos vacía llamada `nidoreserva`.

Se puede hacer desde phpMyAdmin o mediante el cliente de MySQL:

```sql
CREATE DATABASE nidoreserva
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

Selecciona la base de datos recién creada e importa el archivo:

```text
database/structure.sql
```

Puedes importar el archivo desde la pestaña **Importar** de phpMyAdmin. También puedes utilizar el cliente de MySQL desde la terminal:

```bash
mysql -u root -p nidoreserva < database/structure.sql
```

El comando anterior solicitará la contraseña del usuario de MySQL. Si tu configuración local no tiene contraseña, puedes utilizar `mysql -u root nidoreserva < database/structure.sql`.

El archivo SQL crea las tablas necesarias e introduce datos de ejemplo para poder probar la aplicación.

**Importante:** importa el archivo en una base de datos vacía. El script no está diseñado para volver a ejecutarse sobre tablas que ya existen.

### 4. Configurar las variables de entorno

Crea un archivo `.env` en la raíz del proyecto a partir de `.env.example`.

En Git Bash puedes hacerlo con:

```bash
cp .env.example .env
```

Abre `.env` y configura los datos de conexión correspondientes a tu entorno:

```dotenv
DB_HOST=localhost
DB_PORT=3306
DB_NAME=nidoreserva
DB_USER=root
DB_PASSWORD=
```

Significado de las variables:

| Variable | Descripción |
|---|---|
| `DB_HOST` | Host del servidor de base de datos. |
| `DB_PORT` | Puerto del servidor de base de datos. |
| `DB_NAME` | Nombre de la base de datos. |
| `DB_USER` | Usuario de la base de datos. |
| `DB_PASSWORD` | Contraseña del usuario. Puede estar vacía en una instalación local que no requiera contraseña. |

Los valores anteriores son un ejemplo para desarrollo local, no una configuración recomendada para producción. Adapta el usuario, la contraseña, el host y el puerto a tu instalación.

El archivo `.env` está excluido del control de versiones. No subas credenciales ni secretos al repositorio.

### 5. Iniciar la aplicación

Desde la raíz del proyecto, ejecuta:

```bash
php -S localhost:8000 -t public public/router.php
```

Si utilizas Git Bash en Windows y el ejecutable de PHP de XAMPP no está disponible en el `PATH`, puedes indicar su ruta directamente:

```bash
/c/xampp/php/php.exe -S localhost:8000 -t public public/router.php
```

Mantén la terminal abierta mientras utilizas la aplicación.

Abre en el navegador:

http://localhost:8000

El servidor integrado de PHP está destinado al desarrollo local y no debe considerarse una configuración de producción.

### 6. Comprobar la instalación

Una vez iniciada la aplicación:

1. Abre la página principal y comprueba que se muestran las reservas de ejemplo.
2. Aplica filtros por estado, fechas y nombre del huésped.
3. Abre el detalle de una reserva y consulta su historial de eventos.
4. Accede al formulario de creación y prueba una reserva con datos válidos.
5. Confirma una reserva pendiente y comprueba que el historial registra el evento.
6. Prueba la cancelación de una reserva y verifica el estado y su historial.

Para probar las operaciones de escritura, utiliza preferiblemente una base de datos de prueba. La aplicación no debe utilizar datos reales de clientes sin incorporar las medidas de seguridad y privacidad necesarias.

## Uso de la aplicación

Las principales páginas web son:

| Ruta | Descripción |
|---|---|
| `/` | Listado de reservas. |
| `/reservations/new` | Formulario de creación. |
| `/reservations/{id}` | Detalle e historial de eventos de una reserva. |

En las rutas de detalle, `{id}` representa el identificador de la reserva.

El listado permite combinar los filtros disponibles. La creación se realiza desde el formulario y las operaciones de consulta, creación y cambio de estado se apoyan en la API.

## API REST

La API utiliza JSON para las operaciones de creación y cambio de estado, y devuelve las respuestas de datos con una estructura común.

### Endpoints disponibles

| Método | Ruta | Descripción | Respuesta satisfactoria |
|---|---|---|---|
| `GET` | `/api/reservations` | Listar y filtrar reservas. | `200 OK` |
| `GET` | `/api/reservations/{id}` | Obtener una reserva por su identificador. | `200 OK` |
| `POST` | `/api/reservations` | Crear una reserva. | `201 Created` |
| `PATCH` | `/api/reservations/{id}/status` | Cambiar el estado de una reserva. | `200 OK` |

### 1. Listar reservas

Petición:

```http
GET /api/reservations
```

La consulta admite los siguientes parámetros opcionales:

| Parámetro | Descripción |
|---|---|
| `estado` | Filtra por estado exacto: `pendiente`, `confirmada` o `cancelada`. |
| `fecha_entrada_desde` | Fecha de entrada mínima, incluida. |
| `fecha_entrada_hasta` | Fecha de entrada máxima, incluida. |
| `nombre_huesped` | Busca coincidencias parciales en el nombre del huésped. |

Las fechas utilizan el formato `YYYY-MM-DD`.

Los filtros pueden combinarse. Cuando se aplican varios, todos deben cumplirse. Los resultados se ordenan por fecha de creación descendente.

Ejemplo:

```http
GET /api/reservations?estado=confirmada&fecha_entrada_desde=2026-11-01&fecha_entrada_hasta=2026-11-30&nombre_huesped=Maria
```

Respuesta satisfactoria: `200 OK`.

```json
{
  "data": [
    {
      "ID": "identificador-de-ejemplo",
      "NOMBRE_HUESPED": "Maria García",
      "ESTADO": "confirmada"
    }
  ]
}
```

El objeto anterior ilustra la estructura de los campos; la respuesta real contiene los datos disponibles de la reserva y los valores reales de la base de datos.

### 2. Obtener el detalle de una reserva

Petición:

```http
GET /api/reservations/{id}
```

Respuesta satisfactoria: `200 OK`, con la reserva dentro de `data`.

Si no existe una reserva con el identificador indicado, la API devuelve `404 Not Found`:

```json
{
  "error": "Reservation not found."
}
```

### 3. Crear una reserva

Petición:

```http
POST /api/reservations
Content-Type: application/json
```

Ejemplo de cuerpo JSON:

```json
{
  "nombre_huesped": "Laura Martínez",
  "email_huesped": "laura@example.com",
  "nombre_alojamiento": "Apartamento Centro",
  "fecha_entrada": "2099-06-10",
  "fecha_salida": "2099-06-14",
  "importe": 320.00,
  "notas": "Llegada por la tarde"
}
```

Las fechas futuras del ejemplo son ilustrativas; puedes sustituirlas por las fechas válidas que necesites para tu prueba.

Los campos obligatorios son:

- `nombre_huesped`
- `email_huesped`
- `nombre_alojamiento`
- `fecha_entrada`
- `fecha_salida`
- `importe`

El campo `notas` es opcional.

El servidor genera el identificador y asigna automáticamente el estado inicial `pendiente`. También registra el evento de creación.

Si la operación termina correctamente, devuelve `201 Created` y la reserva creada dentro de `data`.

Los datos inválidos producen `400 Bad Request`. El cuerpo de la petición debe ser un objeto JSON válido, no una cadena, una lista ni un valor JSON de otro tipo.

### 4. Cambiar el estado de una reserva

Petición:

```http
PATCH /api/reservations/{id}/status
Content-Type: application/json
```

Para confirmar una reserva pendiente:

```json
{
  "estado": "confirmada"
}
```

Para cancelar una reserva pendiente o confirmada:

```json
{
  "estado": "cancelada"
}
```

El endpoint solo acepta `confirmada` o `cancelada`. No permite establecer `pendiente` mediante esta operación.

Cuando el cambio se completa correctamente, devuelve `200 OK`, con la reserva actualizada dentro de `data`. El cambio también genera un evento en el historial.

Las transiciones permitidas son:

- `pendiente` → `confirmada`
- `pendiente` → `cancelada`
- `confirmada` → `cancelada`

No se permiten otras transiciones; por ejemplo, no se puede reactivar una reserva cancelada mediante este endpoint.

### Códigos HTTP y errores

| Código | Significado |
|---|---|
| `200 OK` | Consulta o cambio de estado completado. |
| `201 Created` | Reserva creada correctamente. |
| `400 Bad Request` | JSON inválido, datos no válidos o transición de estado no permitida. |
| `404 Not Found` | No existe la reserva solicitada. |
| `500 Internal Server Error` | Error inesperado en el servidor. |

Las respuestas de error utilizan un objeto JSON con la propiedad `error`. Por ejemplo:

```json
{
  "error": "The guest email address is invalid."
}
```

Los mensajes concretos dependen de la validación que falle. En los errores inesperados se devuelve un mensaje genérico, sin exponer los detalles internos de la excepción.

## Modelo de datos

El proyecto utiliza dos tablas principales, definidas en `database/structure.sql`.

### RESERVA

Almacena la información de cada reserva:

- `ID`: identificador único.
- `NOMBRE_HUESPED`: nombre del huésped.
- `EMAIL_HUESPED`: dirección de correo electrónico.
- `NOMBRE_ALOJAMIENTO`: alojamiento reservado.
- `FECHA_ENTRADA`: fecha de entrada.
- `FECHA_SALIDA`: fecha de salida.
- `ESTADO`: estado actual de la reserva.
- `IMPORTE`: importe de la reserva.
- `NOTAS`: información adicional.
- `FECHA_CREACION`: fecha de creación del registro.

### RESERVA_EVENTO

Almacena el historial de eventos:

- `ID`: identificador del evento.
- `RESERVA_ID`: referencia a la reserva asociada.
- `TIPO`: tipo de evento.
- `DESCRIPCION`: descripción del evento.
- `FECHA_CREACION`: fecha de registro del evento.

La relación entre ambas tablas permite consultar el historial de una reserva y mantener trazabilidad de sus cambios.

Los eventos de creación y de cambio de estado se registran en la misma transacción que la operación correspondiente. Si alguna de las operaciones de base de datos falla, se revierte la transacción para evitar guardar el cambio sin su evento asociado.

## Arquitectura

El código está organizado por responsabilidades:

```text
src/
├── Controller/
│   ├── ReservationController.php
│   └── WebController.php
├── Database/
│   └── DatabaseConnection.php
├── Repository/
│   ├── ReservationRepository.php
│   └── ReservationEventRepository.php
├── Router/
│   └── Router.php
└── Service/
    ├── ReservationService.php
    └── TemplateRenderer.php

public/
├── css/
│   └── styles.css
├── js/
│   └── reservations.js
├── index.php
└── router.php

templates/
├── base.html.twig
├── error.html.twig
└── reservations/
    ├── index.html.twig
    ├── new.html.twig
    └── show.html.twig

database/
└── structure.sql
```

Responsabilidades principales:

- **Controller:** recibe las peticiones y prepara las respuestas HTTP o las vistas.
- **Service:** implementa las reglas de negocio, las validaciones de las reservas y las transacciones.
- **Repository:** centraliza las consultas y operaciones de persistencia.
- **Database:** crea la conexión PDO a partir de la configuración del entorno.
- **Router:** dirige las peticiones hacia los controladores correspondientes.
- **TemplateRenderer:** configura y utiliza Twig para renderizar las plantillas.
- **Templates:** contiene las vistas de la interfaz, incluida la plantilla base.
- **Public:** contiene el punto de entrada web y los recursos públicos de CSS y JavaScript.
- **database:** contiene el SQL de creación y los datos de ejemplo.

Esta separación evita concentrar las consultas SQL, las reglas de negocio y la presentación en un único archivo, y facilita el mantenimiento y la evolución del proyecto.

## Validaciones y reglas de negocio

La validación del servidor se ejecuta antes de crear una reserva.

- Los campos obligatorios deben estar presentes y no estar vacíos.
- El email debe tener un formato válido.
- Las fechas deben corresponder al formato `YYYY-MM-DD`.
- La fecha de salida debe ser posterior a la fecha de entrada.
- La fecha de entrada no puede estar en el pasado.
- El importe debe ser numérico y no negativo.
- El estado inicial es `pendiente`.
- Los cambios de estado deben respetar las transiciones permitidas.
- Cada creación y cambio de estado debe registrar su correspondiente evento.

Las validaciones del cliente ayudan a mejorar la experiencia de uso, pero no sustituyen las comprobaciones del servidor.

## Seguridad

La aplicación incorpora varias medidas básicas:

- **Consultas preparadas:** PDO utiliza parámetros preparados para los valores recibidos en las consultas SQL.
- **Configuración externa:** los datos de conexión se leen desde `.env`, excluido del repositorio.
- **Manejo de errores:** los errores inesperados de la API se responden con un mensaje genérico, evitando revelar detalles internos.
- **Escape de plantillas:** Twig proporciona escape automático de salida cuando se utiliza con su configuración habitual y los contextos adecuados.
- **Validación en servidor:** se comprueban los datos de las reservas y las transiciones de estado antes de persistir los cambios.

Estas medidas constituyen una base de seguridad, pero no hacen que la aplicación esté lista automáticamente para producción.

Antes de publicar un servicio accesible por terceros, se deberían evaluar e implementar, según el entorno:

- Autenticación y autorización.
- Protección CSRF para las operaciones web basadas en sesiones o credenciales de navegador, cuando corresponda.
- Restricciones de acceso y permisos de base de datos.
- HTTPS y gestión segura de secretos.
- Límites de peticiones y controles frente a abuso.
- Registros, monitorización y gestión de errores.
- Políticas de copias de seguridad y recuperación.
- Revisión de las cabeceras HTTP y de la configuración del servidor.

## Escalabilidad y mejoras futuras

El proyecto es una base para una aplicación de gestión de reservas. Un escenario de aproximadamente 1.000 reservas por hora requiere pruebas de carga representativas antes de afirmar que el sistema puede sostener ese volumen.

Las necesidades reales dependerán de la distribución de las peticiones, la concurrencia, las consultas ejecutadas, los recursos disponibles y la configuración de PHP y de la base de datos.

Entre las posibles mejoras se encuentran:

- Incorporar paginación en el listado de reservas.
- Evaluar índices de base de datos según los filtros y consultas más frecuentes.
- Analizar los planes de ejecución de SQL.
- Medir los tiempos de respuesta y la concurrencia mediante pruebas de carga.
- Incorporar autenticación y permisos por usuario.
- Añadir pruebas automatizadas de la lógica de negocio y de los endpoints.
- Configurar registros estructurados, monitorización y alertas.
- Definir políticas de copias de seguridad y recuperación.
- Preparar un proceso de despliegue reproducible.
- Evaluar caché u otras optimizaciones cuando las mediciones demuestren que son necesarias.

Las mejoras deberían priorizarse a partir de mediciones y requisitos reales, evitando introducir complejidad antes de que exista una necesidad demostrada.

## Despliegue

**URL pública:** pendiente de despliegue.

La aplicación utiliza PHP y MySQL/MariaDB, por lo que el entorno de producción debe soportar ambos componentes, las extensiones de PHP requeridas y la configuración de las variables de entorno.

El despliegue deberá incluir la preparación de la base de datos, la configuración de secretos, la publicación de los recursos web y la configuración del servidor. El servidor integrado de PHP descrito en la instalación local no está pensado para producción.

Una vez publicada la aplicación, se actualizará esta sección con la URL definitiva y las instrucciones específicas del entorno desplegado.

## Licencia

Proyecto desarrollado con fines de evaluación técnica. No se ha definido una licencia de distribución independiente en este repositorio.