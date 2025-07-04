USE trivia;

ALTER TABLE users
MODIFY COLUMN password VARCHAR(255);

UPDATE `users`
SET `password` = '$2y$10$vjnppd2L/dwuBgTWnk30..pZG9iuYRtOoOWAluQjXJuHgt/tjM6FC';