DROP DATABASE IF EXISTS bd_televisor;
CREATE DATABASE bd_televisor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bd_televisor;

CREATE TABLE usuario (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  ci VARCHAR(20) UNIQUE,
  telefono VARCHAR(20),
  direccion VARCHAR(150),
  correo VARCHAR(100) UNIQUE,
  username VARCHAR(40) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('CLIENTE','ADMINISTRADOR') NOT NULL,
  fecha_nacimiento DATE,
  genero ENUM('M', 'F', 'OTRO'),
  ultimo_login DATETIME,
  avatar_url VARCHAR(255),
  estado TINYINT(1) DEFAULT 1,
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- MARCA
CREATE TABLE marca (
  id_marca INT AUTO_INCREMENT PRIMARY KEY,
  nombre_marca VARCHAR(50) UNIQUE NOT NULL
) ENGINE=InnoDB;

-- CATEGORIA
CREATE TABLE categoria (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  nombre_categoria VARCHAR(50) UNIQUE NOT NULL
) ENGINE=InnoDB;

-- PROVEEDOR
CREATE TABLE proveedor (
  id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  telefono VARCHAR(20),
  direccion VARCHAR(150),
  correo VARCHAR(100)
) ENGINE=InnoDB;

-- PRODUCTO (ESPECÍFICO PARA TVs)
CREATE TABLE producto (
  id_producto INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(10,2) NOT NULL,
  id_marca INT,
  id_categoria INT,
  id_proveedor INT,
  codigo_sku VARCHAR(40) NULL,
  imagen_url VARCHAR(255),
  -- Campos específicos para TVs
  pulgadas DECIMAL(4,1),
  resolucion ENUM('HD', 'FULL HD', '4K', '8K'),
  smart_tv BOOLEAN DEFAULT FALSE,
  hdmi_puertos INT DEFAULT 2,
  usb_puertos INT DEFAULT 1,
  garantia_meses INT DEFAULT 12,
  estado TINYINT(1) DEFAULT 1,
  CONSTRAINT fk_producto_marca FOREIGN KEY (id_marca) REFERENCES marca(id_marca),
  CONSTRAINT fk_producto_categoria FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria),
  CONSTRAINT fk_producto_proveedor FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor)
) ENGINE=InnoDB;

-- INVENTARIO
CREATE TABLE inventario (
  id_inventario INT AUTO_INCREMENT PRIMARY KEY,
  id_producto INT NOT NULL,
  stock_actual INT DEFAULT 0,
  stock_minimo INT DEFAULT 0,
  stock_maximo INT DEFAULT 0,
  actualizado_en DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_inv_producto FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB;

-- CARRITO DE COMPRAS
CREATE TABLE carrito (
  id_carrito INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  agregado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_carrito_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
  CONSTRAINT fk_carrito_producto FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB;

-- VENTA
CREATE TABLE venta (
  id_venta INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  id_usuario INT NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  descuento DECIMAL(10,2) DEFAULT 0.00,
  impuesto DECIMAL(10,2) DEFAULT 0.00,
  total DECIMAL(10,2) NOT NULL,
  metodo_pago ENUM('EFECTIVO','TARJETA','QR','TRANSFERENCIA','CREDITO') NOT NULL,
  estado_pago ENUM('PENDIENTE','PAGADO','ANULADO') DEFAULT 'PAGADO',
  estado_venta ENUM('COMPLETADA','PENDIENTE','CANCELADA') DEFAULT 'COMPLETADA',
  CONSTRAINT fk_venta_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
) ENGINE=InnoDB;

-- DETALLE VENTA
CREATE TABLE detalle_venta (
  id_detalle INT AUTO_INCREMENT PRIMARY KEY,
  id_venta INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_det_venta FOREIGN KEY (id_venta) REFERENCES venta(id_venta),
  CONSTRAINT fk_det_producto FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE producto MODIFY codigo_sku VARCHAR(40) NULL;

INSERT INTO marca (nombre_marca) VALUES 
('Samsung'), ('LG'), ('Sony'), ('TCL'), ('Hisense'),
('Panasonic'), ('Philips'), ('Sharp');

INSERT INTO categoria (nombre_categoria) VALUES 
('LED'), ('OLED'), ('QLED'), ('4K UHD'), ('8K UHD'),
('Smart TV'), ('Android TV');

INSERT INTO proveedor (nombre, telefono, direccion, correo) VALUES 
('ElectroImport S.A.', '23456789', 'Av. Industrial 123', 'contacto@electroimport.com'),
('TecnoDistribuidora', '24567890', 'Calle Comercio 456', 'ventas@tecnodist.com'),
('Digital Supply Co.', '25678901', 'Zona Franca 789', 'info@digitalsupply.com');

INSERT INTO usuario (nombre, correo, username, password_hash, rol) VALUES 
('Administrador', 'admin@tiendatv.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMINISTRADOR');

INSERT INTO producto (nombre, descripcion, precio, id_marca, id_categoria, id_proveedor, codigo_sku, pulgadas, resolucion, smart_tv, hdmi_puertos, usb_puertos, garantia_meses) VALUES 
('Samsung QLED 55" 4K', 'Televisor QLED con Quantum Dot Technology', 899.99, 1, 3, 1, 'SAM-Q55-4K', 55.0, '4K', TRUE, 4, 3, 24),
('LG OLED 65" 4K', 'OLED con perfect black y AI ThinQ', 1299.99, 2, 2, 2, 'LG-O65-4K', 65.0, '4K', TRUE, 4, 2, 24),
('Sony Bravia 43" Full HD', 'Smart TV con Android TV integrado', 499.99, 3, 6, 1, 'SON-B43-FHD', 43.0, 'FULL HD', TRUE, 3, 2, 18),
('TCL 50" 4K Android', 'TV 4K con Google Assistant integrado', 399.99, 4, 4, 3, 'TCL-50-4KA', 50.0, '4K', TRUE, 3, 2, 12);

INSERT INTO inventario (id_producto, stock_actual, stock_minimo, stock_maximo) VALUES 
(1, 15, 5, 50),
(2, 8, 3, 30),
(3, 25, 10, 100),
(4, 20, 8, 80);