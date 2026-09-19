CREATE DATABASE IF NOT EXISTS agenda_medica
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE agenda_medica;

CREATE TABLE pacientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE doctores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    especialidad VARCHAR(120) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE citas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT UNSIGNED NOT NULL,
    doctor_id INT UNSIGNED NOT NULL,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    estado ENUM(
        'pendiente',
        'confirmada',
        'cancelada',
        'atendida'
    ) NOT NULL DEFAULT 'pendiente',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_cita_paciente
        FOREIGN KEY (paciente_id)
        REFERENCES pacientes(id),

    CONSTRAINT fk_cita_doctor
        FOREIGN KEY (doctor_id)
        REFERENCES doctores(id),

    CONSTRAINT chk_cita_horario
        CHECK (fecha_fin > fecha_inicio),

    INDEX idx_citas_doctor_horario (
        doctor_id,
        fecha_inicio,
        fecha_fin
    ),

    INDEX idx_citas_paciente (
        paciente_id
    ),

    INDEX idx_citas_estado (
        estado
    )
);

INSERT INTO pacientes (nombres, apellidos, telefono, correo) VALUES
    ('Laura', 'Mendoza', '555-0101', 'laura.mendoza@example.com'),
    ('Carlos', 'Ramírez', '555-0102', 'carlos.ramirez@example.com'),
    ('Sofía', 'Torres', '555-0103', 'sofia.torres@example.com');

INSERT INTO doctores (nombres, apellidos, especialidad, telefono, correo) VALUES
    ('Elena', 'Vargas', 'Medicina general', '555-0201', 'elena.vargas@example.com'),
    ('Mateo', 'Salas', 'Cardiología', '555-0202', 'mateo.salas@example.com'),
    ('Julia', 'Ríos', 'Pediatría', '555-0203', 'julia.rios@example.com');