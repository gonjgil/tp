CREATE DATABASE IF NOT EXISTS trivia;
USE trivia;

/* GENEROS (tabla) */
CREATE TABLE IF NOT EXISTS gender (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type varchar(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* ROL (tabla) */
CREATE TABLE IF NOT EXISTS rol (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type varchar(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* USUARIOS (tabla) */
CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT NOT NULL PRIMARY KEY, -- Equivale a id_cuenta
        id_gender INT NOT NULL, -- genero
        id_rol INT NOT NULL DEFAULT 3,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- fecha_creacion
        email VARCHAR(100) NOT NULL, -- mail
        username VARCHAR(100) NOT NULL, -- usuario
        password VARCHAR(100) NOT NULL, -- contrasenia
        profile_picture VARCHAR(100) NOT NULL, -- foto_perfil
        birth_date DATE NOT NULL, -- fecha_nacimiento
        name VARCHAR(100) NOT NULL, -- nombre
        last_name VARCHAR(100) NOT NULL, -- apellido
        is_active TINYINT(1) NOT NULL, -- esta_activa
        validation_date TIMESTAMP NULL DEFAULT NULL, -- fecha_validacion
        token VARCHAR(100) DEFAULT NULL,
        total_answers INT(11) NOT NULL DEFAULT 0, -- cantidad_respuestas
        correct_answers INT(11) NOT NULL DEFAULT 0, -- cantidad_correctas
        difficulty FLOAT NOT NULL DEFAULT 1,
        lat FLOAT DEFAULT NULL,
        lng FLOAT DEFAULT NULL,
        age INT(11) GENERATED ALWAYS AS (TIMESTAMPDIFF(YEAR, birth_date, CURDATE())) VIRTUAL, -- edad_calculada
        country VARCHAR(100) NOT NULL, -- pais
        city VARCHAR(100) NOT NULL, -- ciudad
        UNIQUE KEY unique_username (username),
        UNIQUE KEY unique_email (email),
        FOREIGN KEY (id_gender) REFERENCES gender(id),
        FOREIGN KEY (id_rol) REFERENCES rol(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* CATEGORIAS (tabla) */
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* PREGUNTAS (tabla) */
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    creator_id INT NULL, -- ahora permite nulls
    question_text VARCHAR(255) NOT NULL,
    approved TINYINT(1) NOT NULL DEFAULT 0,
    reported TINYINT(1) NOT NULL DEFAULT 0,
    suggested TINYINT(1) NOT NULL DEFAULT 0,
    times_answered INT(11) NOT NULL DEFAULT 0,
    times_incorrect INT(11) NOT NULL DEFAULT 0,
    difficulty FLOAT NOT NULL DEFAULT 100,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (creator_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* RESPUESTAS (tabla) */
CREATE TABLE IF NOT EXISTS answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    answer_text VARCHAR(255) NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES questions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* PARTIDAS (tabla) */
CREATE TABLE IF NOT EXISTS games (
    id_game INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- quien juega
    correct_answers INT NOT NULL DEFAULT 0, -- puntos en esta partida
    total_questions INT NOT NULL DEFAULT 0, -- cuántas preguntas respondió
    start_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    end_time DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* PARTIDAS_PREGUNTAS (tabla) */
CREATE TABLE IF NOT EXISTS game_questions (
    id_game INT NOT NULL,
    question_id INT NOT NULL,
    answered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_game, question_id),
    FOREIGN KEY (id_game) REFERENCES games(id_game) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* REPORTES (tabla) */
CREATE TABLE IF NOT EXISTS reports (
     id             INT          AUTO_INCREMENT PRIMARY KEY,
     id_question    INT          NOT NULL,
     report         TEXT         NOT NULL,
     reported_by    INT          NOT NULL,
     reported_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reported_by)  REFERENCES users(id),
    FOREIGN KEY (id_question)  REFERENCES questions(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* GENEROS (datos) */
INSERT IGNORE INTO gender (type) VALUES
        ('Masculino'),
        ('Femenino'),
        ('Prefiero no decirlo');

/* ROLES (datos) */
INSERT IGNORE INTO rol (type) VALUES
        ('admin'),
        ('editor'),
        ('player');

/* CATEGORIAS (datos) */
INSERT IGNORE INTO categories (name, description) VALUES
    ('Cultura General', 'Preguntas de cultura general'),
    ('Ciencia', 'Preguntas sobre ciencia en general'),
    ('Historia', 'Preguntas sobre hechos historicos');