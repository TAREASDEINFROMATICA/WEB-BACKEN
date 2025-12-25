
CREATE DATABASE bd_empleado;

USE bd_empleado;

CREATE TABLE empleados (
  id_empleado INT PRIMARY KEY AUTO_INCREMENT,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  nombre VARCHAR(50) NOT NULL,
  apellido_paterno VARCHAR(50) NOT NULL,
  apellido_materno VARCHAR(50) NOT NULL,
  turno ENUM('Completo','Medio Turno','Fines de Semana') NOT NULL,
  habilitado TINYINT(1) NOT NULL DEFAULT 1,
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ingresos (
  id_ingreso INT PRIMARY KEY AUTO_INCREMENT,
  id_empleado INT NOT NULL,
  hora TIME NOT NULL,
  fecha DATE NOT NULL,
  FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado)
);

CREATE TABLE salidas (
  id_salidad INT PRIMARY KEY AUTO_INCREMENT,
  id_empleado INT NOT NULL,
  hora TIME NOT NULL,
  fecha DATE NOT NULL,
  FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado)
);

CREATE TABLE pagos (
  id_pago INT PRIMARY KEY AUTO_INCREMENT,
  id_empleado INT NOT NULL,
  horas_trabajadas DECIMAL(5,2) NOT NULL,
  pago DECIMAL(10,2) NOT NULL,
  bonos DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  descuentos DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  fecha DATE NOT NULL,
  FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado)
);
