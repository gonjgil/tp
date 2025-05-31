USE trivia;

/* USUARIOS (datos) */
INSERT IGNORE INTO users (
    id_gender, id_rol, email, username, password, profile_picture,
    birth_date, name, last_name, is_active, country, city, lat, lng
) VALUES
    (
        1, -- id_gender: Masculino
        2, -- id_rol: Editor
        'editor@example.com',
        'editor',
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

INSERT IGNORE INTO users (
    id_gender, id_rol, email, username, password, profile_picture,
    birth_date, name, last_name, is_active, country, city, lat, lng
) VALUES
    (
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

    INSERT IGNORE INTO users (
    id_gender, id_rol, email, username, password, profile_picture,
    birth_date, name, last_name, is_active, country, city, lat, lng
) VALUES
    (
        1, -- id_gender: Masculino
        3, -- id_rol: Jugador
        'admin@player.com',
        'player',
        '123', -- password hasheada
        'uploads/default.png',
        '1985-10-30',
        'Bro',
        'Master',
        1,
        'Argentina',
        'Buenos Aires',
        -34.6037,
        -58.3816
    );

/* PREGUNTAS (datos) */
INSERT INTO questions (question_text, category_id, creator_id, approved, reported) VALUES
    ('Cual es la capital de Francia?', 1, 2, 1, 0),
    ('Que planeta es conocido como planeta rojo?', 2, 2, 1, 0),
    ('Quien escribio "Romeo y Julieta"?', 1, 3, 1, 0),
    ('Cual es es simbolo quimico del agua?', 2, 3, 1, 0),
    ('En que año piso la luna el primer ser humano?', 3, 2, 1, 0);

/* OPCIONES (datos) */
INSERT INTO answers (question_id, answer_text, is_correct) VALUES

    (1, 'Paris', 1),
    (1, 'Berlin', 0),
    (1, 'Madrid', 0),
    (1, 'Roma', 0),

    (2, 'Marte', 1),
    (2, 'Jupiter', 0),
    (2, 'Venus', 0),
    (2, 'Saturno', 0),

    (3, 'William Shakespeare', 1),
    (3, 'Charles Dickens', 0),
    (3, 'Mark Twain', 0),
    (3, 'Jane Austen', 0),

    (4, 'H₂O', 1),
    (4, 'CO₂', 0),
    (4, 'O₂', 0),
    (4, 'NaCl', 0),

    (5, '1969', 1),
    (5, '1959', 0),
    (5, '1979', 0),
    (5, '1989', 0);