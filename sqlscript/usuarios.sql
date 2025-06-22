USE trivia;


INSERT INTO `users` (`id`, `id_gender`, `id_rol`, `created_at`, `email`, `username`, `password`, `profile_picture`, `birth_date`, `name`, `last_name`, `is_active`, `validation_date`, `token`, `total_answers`, `correct_answers`, `difficulty`, `lat`, `lng`, `country`, `city`) VALUES
    (1, 1, 2, '2025-06-21 01:09:49', 'editor@example.com', 'editor', '123', 'uploads/default.png', '1990-05-15', 'Carlos', 'Pérez', 1, NULL, NULL, 0, 0, 1, -34.6037, -58.3816, 'Argentina', 'Buenos Aires'),
    (2, 2, 1, '2025-06-21 01:09:49', 'admin@example.com', 'admin', '123', 'uploads/default.png', '1985-10-30', 'María', 'Gómez', 1, NULL, NULL, 0, 0, 1, -33.4489, -70.6693, 'Chile', 'Santiago'),
    (3, 1, 3, '2025-06-21 01:09:49', 'player@example.com', 'player', '123', 'uploads/bro.jpg', '1985-10-30', 'Bro', 'Master', 1, '2025-06-21 01:09:49', NULL, 100, 85, 0.15, -34.6037, -58.3816, 'Argentina', 'Buenos Aires'),
    (4, 1, 3, '2025-06-21 01:09:49', 'pro@gamer.com', 'pro_gamer', '123', 'uploads/max.jpg', '1990-05-20', 'Max', 'Power', 1, '2025-06-21 01:09:49', NULL, 200, 190, 0.05, -31.4201, -64.1888, 'Argentina', 'Córdoba'),
    (5, 2, 3, '2025-06-21 01:09:49', 'novato1@fail.com', 'novato1', '123', 'uploads/novato1.png', '2002-07-12', 'Lina', 'Test', 1, '2025-06-21 01:09:49', NULL, 80, 30, 0.625, -32.9442, -60.6505, 'Argentina', 'Rosario'),
    (6, 3, 3, '2025-06-21 01:09:49', 'novato2@fail.com', 'novato2', '123', 'uploads/novato2.jpg', '1995-12-03', 'Alex', 'Beginner', 1, '2025-06-21 01:09:49', NULL, 150, 60, 0.6, -32.8908, -68.8272, 'Argentina', 'Mendoza'),
    (7, 1, 3, '2025-06-21 01:10:35', 'johnny@usa.com', 'johnnyNY', '123', 'uploads/johnnyNY.jpg', '1992-03-22', 'Johnny', 'Walker', 1, '2025-06-21 01:10:35', NULL, 120, 90, 0.25, 40.7128, -74.006, 'USA', 'New York'),
    (8, 1, 3, '2025-06-21 01:10:35', 'louis@canada.ca', 'louisT', '123', 'uploads/louisT.jpg', '1988-09-10', 'Louis', 'Snow', 1, '2025-06-21 01:10:35', NULL, 60, 30, 0.5, 43.651, -79.347, 'Canada', 'Toronto'),
    (9, 1, 3, '2025-06-21 01:10:35', 'emily@uk.co', 'emilyUK', '123', 'uploads/emilyUK.jpg', '1995-06-17', 'Emily', 'Smith', 1, '2025-06-21 01:10:35', NULL, 140, 100, 0.285, 51.5074, -0.1278, 'UK', 'London'),
    (10, 2, 3, '2025-06-21 01:10:35', 'claire@fr.fr', 'claireParis', '123', 'uploads/claireParis.jpg', '1993-11-04', 'Claire', 'Dubois', 1, '2025-06-21 01:10:35', NULL, 85, 60, 0.294, 48.8566, 2.3522, 'France', 'Paris'),
    (11, 1, 3, '2025-06-21 01:10:35', 'hans@de.de', 'hansBerlin', '123', 'uploads/hansBerlin.jpg', '1987-01-15', 'Hans', 'Müller', 1, '2025-06-21 01:10:35', NULL, 70, 55, 0.214, 52.52, 13.405, 'Germany', 'Berlin'),
    (12, 1, 3, '2025-06-21 01:10:35', 'akira@jp.jp', 'akiraTokyo', '123', 'uploads/akiraTokyo.jpg', '1999-08-08', 'Akira', 'Tanaka', 1, '2025-06-21 01:10:35', NULL, 0, 0, 1.00, 35.6895, 139.692, 'Japan', 'Tokyo'),
    (13, 2, 3, '2025-06-21 01:10:35', 'sophie@au.com', 'sophieOz', '123', 'uploads/sophieOz.jpg', '2000-02-28', 'Sophie', 'Lee', 1, '2025-06-21 01:10:35', NULL, 95, 60, 0.36, -33.8688, 151.209, 'Australia', 'Sydney'),
    (14, 1, 3, '2025-06-21 01:10:35', 'carlos@br.com', 'carlosRJ', '123', 'uploads/carlosRJ.jpg', '1991-07-19', 'Carlos', 'Silva', 1, '2025-06-21 01:10:35', NULL, 180, 160, 0.111, -22.9068, -43.1729, 'Brazil', 'Rio de Janeiro'),
    (15, 1, 3, '2025-06-21 01:10:35', 'diego@mx.com', 'diegoDF', '123', 'uploads/diegoDF.jpeg', '1996-05-05', 'Diego', '', 1, '2025-06-21 01:10:35', NULL, 130, 90, 0.308, 19.4326, -99.1332, 'Mexico', 'Ciudad de México'),
    (16, 2, 3, '2025-06-21 01:10:35', 'amina@za.com', 'aminaCape', '123', 'uploads/aminaCape.png', '1994-12-01', 'Amina', 'Nkosi', 1, '2025-06-21 01:10:35', NULL, 50, 20, 0.6, -33.9249, 18.4241, 'South Africa', 'Cape Town');
