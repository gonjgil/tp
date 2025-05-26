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
   id INT AUTO_INCREMENT PRIMARY KEY,
   name VARCHAR(100) NOT NULL,
   last_name VARCHAR(100) NOT NULL,
   birth_date DATE NOT NULL,
   gender ENUM('Masculino', 'Femenino', 'Prefiero no cargarlo') NOT NULL,
   country VARCHAR(100) NOT NULL,
   city VARCHAR(100) NOT NULL,
   email VARCHAR(100) NOT NULL,
   username VARCHAR(100) NOT NULL UNIQUE,
   password VARCHAR(255) NOT NULL,
   profile_picture VARCHAR(255),
   user_type ENUM('jugador', 'editor', 'administrador'),
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);

INSERT INTO users (name, last_name, birth_date, gender, country, city, email, username, password, profile_picture, user_type) VALUES (
     'Elena', 'Editor', '1990-05-15', 'Femenino', 'Argentina', 'Buenos Aires', 'elena.editor@example.com',
  'editor1', '123', 'uploads/default.jpg', 'editor'
    );

INSERT INTO users (
    name, last_name, birth_date, gender, country, city, email,username, password, profile_picture, user_type) VALUES (
             'Alan', 'Admin', '1985-02-20', 'Masculino', 'Argentina', 'Córdoba', 'alan.admin@example.com',
             'admin1', '123', 'uploads/default.jpg', 'administrador'
    );
