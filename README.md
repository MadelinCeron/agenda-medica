# Agenda Médica

Sistema web para gestionar y programar citas médicas con calendario interactivo.

## Tecnologías

- Laravel 12
- Vue 3 + Vite
- FullCalendar
- Axios
- MySQL 8.4
- Docker

## Estructura

- `backend/`: API REST Laravel.
- `frontend/`: interfaz Vue y calendario.
- `database/init/`: esquema y datos iniciales de MySQL.
- `docker-compose.yml`: servicio MySQL 8.4.

## Requisitos

PHP 8.2+, Composer, Node.js 20+, npm y Docker Desktop.

## Instalación

Base de datos:

```text
docker compose up -d
```

Backend:

```text
cd backend
composer install
php artisan serve
```

Frontend, en otra terminal:

```text
cd frontend
npm install
npm run dev
```

URLs:

- Laravel: http://127.0.0.1:8000
- Vue: http://localhost:5173
- API: http://127.0.0.1:8000/api

El frontend usa el proxy de Vite para enviar `/api` al backend durante el desarrollo.

## API disponible

- `GET /api/doctores`: lista de doctores.
- `GET /api/pacientes`: lista de pacientes.
- `GET /api/citas`: lista citas y acepta `doctor_id`, `paciente_id`, `desde` y `hasta`.
- `POST /api/citas`: crea una cita y responde `409` ante conflicto de horario.
- `GET /api/citas/{cita}`: consulta el detalle de una cita.
- `PUT /api/citas/{cita}`: actualiza o reprograma una cita.
- `PATCH /api/citas/{cita}/estado`: cambia entre pendiente, confirmada, cancelada y atendida.

Las citas canceladas no bloquean nuevos horarios. Las respuestas son JSON y los errores de validación usan `422`.

## Verificación

```text
cd backend
php artisan route:list --path=api
php artisan test
cd ../frontend
npm run build
```

Para una instalación local nueva, el esquema y los datos de demostración se cargan al crear el volumen MySQL.