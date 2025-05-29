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

----------------------------------------NUESTRA TABLA-------------------------------------------------------------------


CREATE TABLE users (
        id INT(11) NOT NULL, -- Equivale a id_cuenta
        id_gender INT(11) NOT NULL, ------ genero
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


