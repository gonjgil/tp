CREATE DATABASE labanda;
USE labanda;

DROP TABLE IF EXISTS `canciones`;
CREATE TABLE `canciones` (
  `idCancion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) DEFAULT NULL,
  `duracion` int(11) DEFAULT NULL,
  PRIMARY KEY (`idCancion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `canciones` VALUES (1,'cancion1',10),(2,'cancion2',12),(3,'cancion3',15);

DROP TABLE IF EXISTS `presentaciones`;
CREATE TABLE `presentaciones` (
  `idPresentacion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `precio` int(11) DEFAULT NULL,
  PRIMARY KEY (`idPresentacion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `presentaciones` VALUES (1,'Presentacion 1','2020-06-02 22:02:14',10),(2,'Presentacion 2','2020-06-02 22:02:19',10),(3,'Presentacion 3','2020-06-02 22:02:21',10);

create table integrantes
(
    nombre      text null,
    instrumento text null,
    id          int auto_increment
        primary key
);

INSERT INTO integrantes(nombre, instrumento) VALUE ('facu', 'ukelele');




CREATE TABLE users (
        id INT(11) NOT NULL, -- Equivale a id_cuenta
        id_gender INT(11) NOT NULL, -- genero
        id_rol INT(11) NOT NULL DEFAULT 3,
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
        PRIMARY KEY (id),
        UNIQUE KEY unique_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `gender` (
        `id_gender` int(11) NOT NULL,
        `type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `gender` (`id_gender`, `type`) VALUES
        (1, 'Masculino'),
        (2, 'Femenino'),
        (3, 'Prefiero no decirlo');


CREATE TABLE `rol` (
        `id_rol` int(11) NOT NULL,
        `type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE categories (
                                          id_category   INT AUTO_INCREMENT PRIMARY KEY,
                                          name          VARCHAR(100) NOT NULL,
    description   TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE questions (

    id              INT AUTO_INCREMENT PRIMARY KEY,
    question_text   VARCHAR(255)    NOT NULL,
    category_id     INT             NOT NULL,
    creator_id      INT             NOT NULL,
    approved        TINYINT(1)      NOT NULL DEFAULT 0,
    reported        TINYINT(1)      NOT NULL DEFAULT 0,

    FOREIGN KEY (category_id)
    REFERENCES categories(id_category)
    ON DELETE CASCADE,

    FOREIGN KEY (creator_id)
    REFERENCES users(id)
    ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE answers (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    question_id       INT             NOT NULL,
    answer_text       VARCHAR(255)    NOT NULL,
    is_correct        TINYINT(1)      NOT NULL DEFAULT 0,


    FOREIGN KEY (question_id)
    REFERENCES questions(id)
    ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE games (
     id_game          INT AUTO_INCREMENT PRIMARY KEY,
     user_id          INT NOT NULL,                         -- quien juega
     correct_answers  INT    NOT NULL DEFAULT 0,            -- puntos en esta partida
     total_questions  INT    NOT NULL DEFAULT 0,            -- cuántas preguntas respondió
     start_time       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
     end_time         DATETIME NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
    );

INSERT INTO `rol` (`id_rol`, `type`) VALUES
        (1, 'Administrador'),
        (2, 'Editor'),
        (3, 'Jugador');

INSERT INTO users (
    id, id_gender, id_rol, email, username, password, profile_picture,
    birth_date, name, last_name, is_active, country, city, lat, lng
) VALUES
    (
        2, -- id
        1, -- id_gender: Masculino
        2, -- id_rol: Editor
        'editor@example.com',
        'editorUser',
        '123', -- ejemplo de password hasheada con SHA-256
        'uploads/default.png',
        '1990-05-15',
        'Carlos',
        'Pérez',
        1, -- is_active (activo)
        'Argentina',
        'Buenos Aires',
        -34.6037,
        -58.3816
    );

INSERT INTO users (
    id, id_gender, id_rol, email, username, password, profile_picture,
    birth_date, name, last_name, is_active, country, city, lat, lng
) VALUES
    (
        3, -- id
        2, -- id_gender: Femenino
        1, -- id_rol: Administrador
        'admin@example.com',
        'admin',
        '123', -- password hasheada
        'uploads/default.png',
        '1985-10-30',
        'María',
        'Gómez',
        1,
        'Chile',
        'Santiago',
        -33.4489,
        -70.6693
    );



INSERT INTO categories (name, description) VALUES
    ('Cultura General', 'Preguntas de cultura general'),
    ('Ciencia',           'Preguntas sobre ciencia en general'),
    ('Historia',           'Preguntas sobre hechos historicos');


INSERT INTO questions (question_text, category_id, creator_id, approved, reported) VALUES
    ('Cual es la capital de Francia?',              1, 2, 1, 0),
    ('Que planeta es conocido como planeta rojo?',     2, 2, 1, 0),
    ('Quien escribio "Romeo y Julieta"?',               1, 3, 1, 0),
    ('Cual es es simbolo quimico del agua?',      2, 3, 1, 0),
    ('En que año piso la luna el primer ser humano?', 3, 2, 1, 0);


INSERT INTO answers (question_id, answer_text, is_correct) VALUES

    (1, 'Paris',   1),
    (1, 'Berlin',  0),
    (1, 'Madrid',  0),
    (1, 'Roma',    0),


    (2, 'Marte',    1),
    (2, 'Jupiter', 0),
    (2, 'Venus',   0),
    (2, 'Saturno',  0),


    (3, 'William Shakespeare', 1),
    (3, 'Charles Dickens',     0),
    (3, 'Mark Twain',          0),
    (3, 'Jane Austen',         0),


    (4, 'H₂O',    1),
    (4, 'CO₂',    0),
    (4, 'O₂',     0),
    (4, 'NaCl',   0),


    (5, '1969',   1),
    (5, '1959',   0),
    (5, '1979',   0),
    (5, '1989',   0);

