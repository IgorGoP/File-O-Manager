-- Actualizar la tabla 'archivos'
DROP TABLE IF EXISTS archivos;
CREATE TABLE archivos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    ruta VARCHAR(255) NOT NULL,
    propietario_id INT(11),
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Actualizar la tabla 'config'
DROP TABLE IF EXISTS config;
CREATE TABLE config (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    setting_name VARCHAR(255) NOT NULL,
    setting_value VARCHAR(255) NOT NULL
);

-- Actualizar la tabla 'permisos'
DROP TABLE IF EXISTS permisos;
CREATE TABLE permisos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    archivo_id INT(11),
    usuario_id INT(11),
    permiso VARCHAR(50)
);

-- Actualizar la tabla 'usuarios'
DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(15),
    nom_completo VARCHAR(80),
    email VARCHAR(100) UNIQUE NOT NULL,
    contrasena VARCHAR(150),
    idioma VARCHAR(10) DEFAULT 'es',
    avatar VARCHAR(50),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    rol VARCHAR(20) DEFAULT 'usuario' NOT NULL
);

-- Reinsertar el usuario inicial en 'usuarios'
INSERT INTO usuarios (nombre, email, contrasena, idioma, avatar, rol) VALUES
('Admin', 'admin@example.com', '$2y$10$Fhw7s.8L1pzs8HLmNkm7cOWsXUp0YSd8uuHr6x9UCVlto/yCXMt0C', 'es', 'public/avatars/avatar_1.jpg', 'admin');

-- Insertar configuración inicial en 'config'
INSERT INTO config (setting_name, setting_value) VALUES
('logo_path', 'public/logo/Logo_Default.jpg');

