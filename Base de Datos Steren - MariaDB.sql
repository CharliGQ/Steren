-- Uso de la base de datos `Steren`
CREATE DATABASE IF NOT EXISTS Steren;
USE Steren;

-- Creación de la tabla de productos
CREATE TABLE productos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  categoria VARCHAR(255) NOT NULL,
  precio DECIMAL(10, 2) NOT NULL,
  stock INT NOT NULL,
  descripcion TEXT
);

-- Creación de la tabla de categorías
CREATE TABLE categorias (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL UNIQUE
);

-- Creación de la tabla de pedidos
CREATE TABLE pedidos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  fecha_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
  nombre_cliente VARCHAR(255) NOT NULL,
  monto_total DECIMAL(10, 2) NOT NULL
);

-- Creación de la tabla de items de pedidos
CREATE TABLE items_pedido (
  id INT PRIMARY KEY AUTO_INCREMENT,
  pedido_id INT,
  producto_id INT,
  cantidad INT NOT NULL,
  precio DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Creación de la tabla de clientes
CREATE TABLE clientes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  correo VARCHAR(255) NOT NULL UNIQUE,
  telefono VARCHAR(50),
  direccion VARCHAR(255)
);

-- Creación de la tabla de administradores
CREATE TABLE administradores (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario VARCHAR(255) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  correo VARCHAR(255) NOT NULL UNIQUE
);

-- Creación de la tabla de tipos de usuarios
CREATE TABLE tipos_usuario (
  id INT PRIMARY KEY AUTO_INCREMENT,
  tipo_nombre VARCHAR(255) NOT NULL UNIQUE
);

-- Creación de la tabla de empleados
CREATE TABLE empleados (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  correo VARCHAR(255) NOT NULL UNIQUE,
  telefono VARCHAR(50),
  tipo_usuario_id INT,
  FOREIGN KEY (tipo_usuario_id) REFERENCES tipos_usuario(id)
);

-- Tabla de Carrito de Compras
CREATE TABLE carrito_compras (
  id INT PRIMARY KEY AUTO_INCREMENT,
  cliente_id INT,
  producto_id INT,
  cantidad INT NOT NULL,
  fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Tabla de Métodos de Pago
CREATE TABLE metodos_pago (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre_metodo VARCHAR(255) NOT NULL UNIQUE
);

-- Tabla de Pagos
CREATE TABLE pagos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  pedido_id INT,
  metodo_pago_id INT,
  monto_pagado DECIMAL(10, 2) NOT NULL,
  fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
  estado VARCHAR(50) NOT NULL, -- Ej: "completado", "pendiente"
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago(id)
);

-- Tabla de Envíos
CREATE TABLE envios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  pedido_id INT,
  direccion_envio VARCHAR(255) NOT NULL,
  fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
  estado VARCHAR(50) NOT NULL, -- Ej: "en tránsito", "entregado"
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
);

-- Tabla de Opiniones y Comentarios de Productos
CREATE TABLE comentarios_productos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  producto_id INT,
  cliente_id INT,
  comentario TEXT NOT NULL,
  valoracion INT NOT NULL CHECK (valoracion >= 1 AND valoracion <= 5), -- De 1 a 5 estrellas
  fecha_comentario DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (producto_id) REFERENCES productos(id),
  FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

-- Tabla de Descuentos
CREATE TABLE descuentos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre_descuento VARCHAR(255) NOT NULL,
  tipo_descuento VARCHAR(50) NOT NULL CHECK (tipo_descuento IN ('porcentaje', 'cantidad_fija')),
  valor DECIMAL(10, 2) NOT NULL, -- Si es porcentaje, de 0 a 100; si es cantidad fija, será un valor monetario.
  fecha_inicio DATETIME,
  fecha_fin DATETIME
);

-- Tabla de Cupones
CREATE TABLE cupones (
  id INT PRIMARY KEY AUTO_INCREMENT,
  codigo VARCHAR(50) NOT NULL UNIQUE,
  descuento_id INT,
  usado BIT DEFAULT 0, -- 0 = no usado, 1 = usado
  FOREIGN KEY (descuento_id) REFERENCES descuentos(id)
);

-- Tabla de Historial de Compras
CREATE TABLE historial_compras (
  id INT PRIMARY KEY AUTO_INCREMENT,
  cliente_id INT,
  pedido_id INT,
  fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
  total_pagado DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
);

-- Tabla de Historial de Inventario
CREATE TABLE historial_inventario (
  id INT PRIMARY KEY AUTO_INCREMENT,
  producto_id INT,
  cantidad_ajustada INT NOT NULL,
  tipo_ajuste VARCHAR(50) NOT NULL CHECK (tipo_ajuste IN ('entrada', 'salida')),
  fecha_ajuste DATETIME DEFAULT CURRENT_TIMESTAMP,
  descripcion VARCHAR(255), -- Explicación del ajuste
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Tabla de Roles (Autenticación de Usuarios)
CREATE TABLE roles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL UNIQUE
);

-- Tabla de Usuarios (Autenticación de Usuarios)
CREATE TABLE usuarios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  correo VARCHAR(255) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  rol_id INT,
  nombre VARCHAR(255),
  fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- Tabla de Lista de Deseos
CREATE TABLE lista_deseos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  cliente_id INT,
  producto_id INT,
  fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Tabla de Seguimiento de Actividad de Usuarios
CREATE TABLE actividad_usuarios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_id INT,
  accion VARCHAR(255) NOT NULL,
  fecha_actividad DATETIME DEFAULT CURRENT_TIMESTAMP,
  ip VARCHAR(50), -- Dirección IP del usuario
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
