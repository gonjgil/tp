USE trivia;

INSERT INTO games (user_id, correct_answers, total_questions, start_time, end_time) VALUES
-- player
(3, 8, 10, NOW() - INTERVAL 10 DAY, NOW() - INTERVAL 9 DAY),
(3, 5, 10, NOW() - INTERVAL 5 DAY, NULL),

-- pro_gamer
(4, 10, 10, NOW() - INTERVAL 15 DAY, NOW() - INTERVAL 14 DAY),
(4, 9, 10, NOW() - INTERVAL 2 DAY, NULL),

-- novato1
(5, 3, 10, NOW() - INTERVAL 20 DAY, NOW() - INTERVAL 19 DAY),
(5, 2, 10, NOW() - INTERVAL 3 DAY, NULL),

-- novato2
(6, 4, 10, NOW() - INTERVAL 8 DAY, NOW() - INTERVAL 7 DAY),
(6, 5, 15, NOW() - INTERVAL 1 DAY, NULL);