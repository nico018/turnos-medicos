-- Crear base y usarla
CREATE DATABASE IF NOT EXISTS consultorio
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE consultorio;

-- Médicos
CREATE TABLE IF NOT EXISTS medicos (
  id_medico INT AUTO_INCREMENT PRIMARY KEY,
  nombre_completo VARCHAR(120) NOT NULL,
  especialidad VARCHAR(100) NOT NULL,
  foto_url VARCHAR(255) NULL
);

-- Pacientes
CREATE TABLE IF NOT EXISTS pacientes (
  id_paciente INT AUTO_INCREMENT PRIMARY KEY,
  nombre_completo VARCHAR(120) NOT NULL,
  dni VARCHAR(20) NOT NULL,
  email VARCHAR(120) NOT NULL,
  telefono VARCHAR(30) NOT NULL
);

-- Turnos
CREATE TABLE IF NOT EXISTS turnos (
  id_turno INT AUTO_INCREMENT PRIMARY KEY,
  id_medico INT NOT NULL,
  id_paciente INT NOT NULL,
  fecha DATE NOT NULL,
  hora TIME NOT NULL,
  estado ENUM('reservado','confirmado','cancelado','atendido') DEFAULT 'reservado',
  motivo_cancelacion VARCHAR(255) NULL,
  codigo_reserva CHAR(10) NOT NULL UNIQUE,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  creado_por VARCHAR(60) DEFAULT 'publico',
  actualizado_en TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  actualizado_por VARCHAR(60) DEFAULT NULL,
  FOREIGN KEY (id_medico) REFERENCES medicos(id_medico),
  FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente),
  UNIQUE KEY uk_unico_turno (id_medico, fecha, hora)
);

-- Agenda de médicos
CREATE TABLE IF NOT EXISTS medicos_horarios (
  id_horario INT AUTO_INCREMENT PRIMARY KEY,
  id_medico INT NOT NULL,
  dia_semana TINYINT NOT NULL,              -- 1=lunes ... 7=domingo
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  duracion_minutos INT NOT NULL DEFAULT 20,
  FOREIGN KEY (id_medico) REFERENCES medicos(id_medico)
);

-- Feriados / Bloqueos puntuales
CREATE TABLE IF NOT EXISTS feriados (
  id_feriado INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATE NOT NULL,
  motivo VARCHAR(150) NOT NULL
);

-- Auditoría
CREATE TABLE IF NOT EXISTS auditoria (
  id_auditoria INT AUTO_INCREMENT PRIMARY KEY,
  entidad VARCHAR(40) NOT NULL,
  accion VARCHAR(30) NOT NULL,
  detalle TEXT NULL,
  usuario VARCHAR(60) NOT NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Datos de ejemplo
INSERT INTO medicos (nombre_completo, especialidad, foto_url) VALUES
('Dra. Lucía Fernández', 'Clínica Médica', 'imagenes/lucia.jpg'),
('Dr. Martín Ríos', 'Cardiología', 'imagenes/martin.jpg'),
('Dra. Sofía Gómez', 'Pediatría', 'imagenes/sofia.jpg'),
('Dr. Alejandro Ruiz', 'Dermatología', 'imagenes/alejandro.jpg'),
('Dra. Paula Navarro', 'Ginecología', 'imagenes/paula.jpg'),
('Dr. Diego Salas', 'Traumatología', 'imagenes/diego.jpg'),
('Dra. Camila Ortiz', 'Oftalmología', 'imagenes/camila.jpg'),
('Dr. Esteban López', 'Neurología', 'imagenes/esteban.jpg');

-- Agenda ejemplo para médico 1
INSERT INTO medicos_horarios (id_medico, dia_semana, hora_inicio, hora_fin, duracion_minutos) VALUES
(1, 1, '09:00:00', '12:00:00', 20),
(1, 3, '09:00:00', '12:00:00', 20);
