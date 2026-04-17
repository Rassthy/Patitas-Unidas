CREATE DATABASE IF NOT EXISTS patitas_unidas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE patitas_unidas;

-- ==============================================================================
-- 1. USUARIOS Y CONFIGURACIÓN (Registro Estricto y Seguro)
-- ==============================================================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL, -- El famoso @usuario
    dni_nie VARCHAR(15) UNIQUE NOT NULL,  -- Obligatorio para evitar multicuentas en España
    telefono VARCHAR(20) UNIQUE NOT NULL, -- Obligatorio para 2FA o contacto
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    tipo ENUM('usuario', 'protectora', 'organizacion', 'admin') NOT NULL DEFAULT 'usuario',
    descripcion TEXT,
    fecha_nacimiento DATE,
    foto_perfil VARCHAR(255),
    banner VARCHAR(255),
    provincia VARCHAR(50), 
    ciudad VARCHAR(100),
    email_verificado BOOLEAN DEFAULT FALSE,
    telefono_verificado BOOLEAN DEFAULT FALSE,
    activo BOOLEAN DEFAULT TRUE, 
    motivo_baja VARCHAR(255) NULL, -- MEJORA: Para saber por qué un admin baneó a alguien
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE user_settings (
    user_id INT PRIMARY KEY,
    idioma ENUM('es', 'en') DEFAULT 'es',
    tema ENUM('claro', 'oscuro', 'sistema') DEFAULT 'sistema',
    ver_apellidos BOOLEAN DEFAULT TRUE,
    ver_fecha_nac BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==============================================================================
-- 2. PERFILES: COMENTARIOS, LIKES E INSIGNIAS
-- ==============================================================================

CREATE TABLE profile_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    target_user_id INT NOT NULL, 
    author_id INT NOT NULL, 
    parent_comment_id INT NULL,  -- Para respuestas en el perfil (1 solo nivel)
    rating INT NULL CHECK (rating >= 1 AND rating <= 5), 
    comentario TEXT NOT NULL,    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_comment_id) REFERENCES profile_comments(id) ON DELETE CASCADE
);

CREATE TABLE profile_comment_likes (
    comment_id INT NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY (comment_id, user_id),
    FOREIGN KEY (comment_id) REFERENCES profile_comments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    icono_url VARCHAR(255)
);

CREATE TABLE user_badges (
    user_id INT NOT NULL,
    badge_id INT NOT NULL,
    fecha_obtencion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, badge_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
);

-- ==============================================================================
-- 3. ÁREA PRIVADA: MASCOTAS, VACUNAS Y RECORDATORIOS
-- ==============================================================================

CREATE TABLE pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    especie VARCHAR(50), 
    raza VARCHAR(50),    
    edad INT,            
    descripcion TEXT,
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE pet_vaccines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    nombre_vacuna VARCHAR(100) NOT NULL,
    fecha_administracion DATE NOT NULL,
    proxima_dosis DATE NULL,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE
);

CREATE TABLE pet_reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    mensaje TEXT,
    fecha_alarma DATETIME NOT NULL,
    notificado BOOLEAN DEFAULT FALSE, 
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE
);

-- ==============================================================================
-- 4. FORO Y PUBLICACIONES (Desvinculado del área privada)
-- ==============================================================================

CREATE TABLE forum_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL 
);

INSERT INTO forum_categories (nombre) VALUES ('Adoptar mascota'), ('Mascota perdida o robada'), ('Apoyar animales');

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    author_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NOT NULL,
    animal_nombre VARCHAR(100) NULL,
    animal_especie VARCHAR(50) NULL,
    animal_raza VARCHAR(50) NULL,
    provincia VARCHAR(50) NOT NULL,
    ciudad VARCHAR(100) NOT NULL,
    latitud DECIMAL(10,8) NULL,  -- MEJORA: Coordenadas para un mapa interactivo
    longitud DECIMAL(11,8) NULL, -- MEJORA: Coordenadas para un mapa interactivo
    estado ENUM('activa', 'en_revision', 'cerrada') DEFAULT 'activa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES forum_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE post_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE post_likes (
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE post_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    author_id INT NOT NULL,
    parent_comment_id INT NULL, -- Evita escalera infinita, el backend solo apunta al comentario padre principal
    comentario TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_comment_id) REFERENCES post_comments(id) ON DELETE CASCADE
);

CREATE TABLE post_comment_likes (
    comment_id INT NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY (comment_id, user_id),
    FOREIGN KEY (comment_id) REFERENCES post_comments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==============================================================================
-- 5. MENSAJERÍA PRIVADA Y GRUPAL
-- ==============================================================================

CREATE TABLE chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    is_group BOOLEAN DEFAULT FALSE,
    nombre_grupo VARCHAR(100) NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE chat_participants (
    chat_id INT NOT NULL,
    user_id INT NOT NULL,
    estado ENUM('pendiente', 'aceptado', 'rechazado') DEFAULT 'pendiente', 
    is_admin BOOLEAN DEFAULT FALSE, 
    PRIMARY KEY (chat_id, user_id),
    FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chat_id INT NOT NULL,
    sender_id INT NOT NULL,
    tipo_contenido ENUM('texto', 'imagen', 'enlace') DEFAULT 'texto', -- revisar
    contenido TEXT NOT NULL, -- revisar
    leido BOOLEAN DEFAULT FALSE, 
    fecha_lectura TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==============================================================================
-- 6. SISTEMA GLOBAL DE DENUNCIAS Y NOTIFICACIONES
-- ==============================================================================

CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporter_id INT NOT NULL, -- Quién denuncia
    reported_user_id INT NULL, -- A QUIÉN denuncian (el culpable, muy útil para banear)
    tipo_entidad ENUM('perfil', 'post', 'post_comentario', 'perfil_comentario', 'mensaje_chat') NOT NULL,
    entidad_id INT NOT NULL, -- ID del comentario, post, etc.
    motivo TEXT NOT NULL,
    estado ENUM('pendiente', 'en_revision', 'aceptado', 'rechazado') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reported_user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tipo ENUM('mensaje', 'rating', 'comentario_post', 'recordatorio_mascota', 'sistema', 'reporte') NOT NULL,
    titulo VARCHAR(100) NOT NULL, -- Extra claridad en la UI
    mensaje TEXT NOT NULL,
    enlace_url VARCHAR(255) NULL,
    leida BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==============================================================================
-- 7. ÍNDICES DE RENDIMIENTO (MEJORA PROFESIONAL)
-- ==============================================================================
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_posts_ubicacion ON posts(provincia, ciudad);
CREATE INDEX idx_posts_estado ON posts(estado);