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
    ),

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
    birth_date, name, last_name, is_active, validation_date, token, total_answers, correct_answers, difficulty, lat, lng, country, city
) VALUES
(
    1, -- Masculino
    3, -- Jugador
    'player@example.com',
    'player',
    '123',
    'uploads/bro.jpg',
    '1985-10-30',
    'Bro',
    'Master',
    1,
    NOW(),
    NULL,
    100,
    85,
    0.15, -- dificultad baja → buen jugador
    -34.6037,
    -58.3816,
    'Argentina',
    'Buenos Aires'
),

(
    1, -- Masculino
    3,
    'pro@gamer.com',
    'pro_gamer',
    '123',
    'uploads/max.jpg',
    '1990-05-20',
    'Max',
    'Power',
    1,
    NOW(),
    NULL,
    200,
    190,
    0.05, -- excelente jugador (95% aciertos)
    -34.60,
    -58.38,
    'Argentina',
    'Córdoba'
),

(
    2, -- Femenino
    3,
    'novato1@fail.com',
    'novato1',
    '123',
    'uploads/lina.png',
    '2002-07-12',
    'Lina',
    'Test',
    1,
    NOW(),
    NULL,
    80,
    30,
    0.625, -- dificultad más alta (50 respuestas incorrectas de 80)
    -31.42,
    -64.18,
    'Argentina',
    'Rosario'
),

(
    3, -- Prefiero no decirlo
    3,
    'novato2@fail.com',
    'novato2',
    '123',
    'uploads/default.png',
    '1995-12-03',
    'Alex',
    'Beginner',
    1,
    NOW(),
    NULL,
    150,
    60,
    0.6, -- 90 errores sobre 150
    -32.89,
    -68.84,
    'Argentina',
    'Mendoza'
);