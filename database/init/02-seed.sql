USE agenda_medica;

INSERT INTO pacientes (
    nombres,
    apellidos,
    telefono,
    correo
)
VALUES
(
    'Carlos',
    'Ramirez',
    '5555-1001',
    'carlos.ramirez@example.com'
),
(
    'Maria',
    'Lopez',
    '5555-1002',
    'maria.lopez@example.com'
),
(
    'Andrea',
    'Morales',
    '5555-1003',
    'andrea.morales@example.com'
);

INSERT INTO doctores (
    nombres,
    apellidos,
    especialidad,
    telefono,
    correo
)
VALUES
(
    'Luis',
    'Herrera',
    'Medicina General',
    '5555-2001',
    'luis.herrera@clinica.com'
),
(
    'Sofia',
    'Martinez',
    'Pediatria',
    '5555-2002',
    'sofia.martinez@clinica.com'
),
(
    'Fernando',
    'Castillo',
    'Cardiologia',
    '5555-2003',
    'fernando.castillo@clinica.com'
);

INSERT INTO citas (
    paciente_id,
    doctor_id,
    fecha_inicio,
    fecha_fin,
    motivo,
    estado
)
VALUES
(
    1,
    1,
    '2026-09-21 09:00:00',
    '2026-09-21 09:30:00',
    'Consulta general',
    'confirmada'
),
(
    2,
    2,
    '2026-09-21 10:00:00',
    '2026-09-21 10:45:00',
    'Consulta pediatrica',
    'pendiente'
),
(
    3,
    3,
    '2026-09-22 11:00:00',
    '2026-09-22 11:30:00',
    'Control cardiologico',
    'atendida'
);