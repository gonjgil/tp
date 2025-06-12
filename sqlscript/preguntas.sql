USE trivia;

/* PREGUNTAS (datos) */
INSERT INTO questions (id, question_text, category_id, creator_id, approved, reported, difficulty, times_answered, times_incorrect) VALUES
    (1, 'Cual es la capital de Francia?', 1, 2, 1, 0, 90, 10, 9),
    (2, 'Que planeta es conocido como planeta rojo?', 2, 2, 1, 1, 30, 10, 3),
    (3, 'Quien escribio "Romeo y Julieta"?', 1, 3, 1, 0, 60, 10, 6),
    (4, 'Cual es es simbolo quimico del agua?', 2, 3, 1, 0, 90, 10, 9),
    (5, 'En que año piso la luna el primer ser humano?', 3, 2, 1, 1, 60, 10, 6),
    (6, 'Cuál es el idioma más hablado del mundo?', 1, 2, 1, 0, 60, 10, 6),
    (7, 'Quién pintó la Mona Lisa?', 1, 2, 1, 0, 30, 10, 3),
    (8, 'Qué órgano del cuerpo humano bombea sangre?', 2, 2, 1, 0, 30, 10, 3),
    (9, 'Cuál es la fórmula química del dióxido de carbono?', 2, 3, 1, 0, 60, 10, 6),
    (10, 'Qué científico desarrolló la teoría de la relatividad?', 2, 3, 1, 0, 60, 10, 6),
    (11, 'En qué año comenzó la Segunda Guerra Mundial?', 3, 2, 1, 0, 90, 10, 9),
    (12, 'Quién fue el primer presidente de los Estados Unidos?', 3, 3, 1, 0, 60, 10, 6),
    (13, 'Cuál fue la civilización que construyó las pirámides de Egipto?', 3, 2, 1, 0, 90, 10, 9),
    (14, 'Cuál es la moneda oficial del Reino Unido?', 1, 3, 1, 0, 60, 10, 6),
    (15, 'Qué país tiene forma de bota?', 1, 2, 1, 0, 30, 10, 3);

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
    (5, '1989', 0),

    (6, 'Chino mandarín', 1),
    (6, 'Español', 0),
    (6, 'Inglés', 0),
    (6, 'Árabe', 0),

    (7, 'Leonardo da Vinci', 1),
    (7, 'Vincent van Gogh', 0),
    (7, 'Pablo Picasso', 0),
    (7, 'Michelangelo', 0),

    (8, 'Corazón', 1),
    (8, 'Pulmón', 0),
    (8, 'Riñón', 0),
    (8, 'Hígado', 0),

    (9, 'CO₂', 1),
    (9, 'H₂O', 0),
    (9, 'O₃', 0),
    (9, 'NaCl', 0),

    (10, 'Albert Einstein', 1),
    (10, 'Isaac Newton', 0),
    (10, 'Galileo Galilei', 0),
    (10, 'Nikola Tesla', 0),

    (11, '1939', 1),
    (11, '1914', 0),
    (11, '1945', 0),
    (11, '1929', 0),

    (12, 'George Washington', 1),
    (12, 'Thomas Jefferson', 0),
    (12, 'Abraham Lincoln', 0),
    (12, 'John Adams', 0),

    (13, 'Egipcia', 1),
    (13, 'Romana', 0),
    (13, 'Griega', 0),
    (13, 'Mesopotámica', 0),

    (14, 'Libra esterlina', 1),
    (14, 'Euro', 0),
    (14, 'Franco suizo', 0),
    (14, 'Dólar', 0),

    (15, 'Italia', 1),
    (15, 'España', 0),
    (15, 'Grecia', 0),
    (15, 'Portugal', 0);