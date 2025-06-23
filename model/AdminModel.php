<?php
class AdminModel {
    /** @var mysqli */
    private $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    public function getQuestionsByCategory(array $filters): array
    {
        $from       = $filters['from']       ?? '1970-01-01';
        $to         = $filters['to']         ?? date('Y-m-d');
        $creator_id = $filters['creator_id'] ?? 'all';

        $sql = "
      SELECT 
        c.name     AS category,
        COUNT(q.id) AS total
      FROM categories c
      LEFT JOIN questions q
        ON q.category_id = c.id
       AND DATE(q.created_at) BETWEEN ? AND ?
    ";
        $types  = 'ss';
        $params = [$from, $to];

        if ($creator_id !== 'all') {
            $sql     .= " AND q.creator_id = ? ";
            $types    .= 'i';
            $params[] = $creator_id;
        }

        $sql .= " GROUP BY c.id, c.name";

        $stmt = $this->database->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        if (method_exists($stmt, 'get_result')) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->bind_result($category, $total);
        $rows = [];
        while ($stmt->fetch()) {
            $rows[] = ['category' => $category, 'total' => (int)$total];
        }
        return $rows;
    }

    public function getQuestionCreators(): array
    {
        $sql = "
        SELECT DISTINCT
            u.id          AS creator_id,
            u.username    AS username
          FROM questions q
          JOIN users     u ON q.creator_id = u.id
    ";
        return $this->database->query($sql);
    }

    public function getQuestionsPerDay(array $filters): array
    {
        $sql = "
      SELECT DATE(created_at) AS fecha,
             COUNT(id)         AS total
        FROM questions
    ";

        $conds  = [];
        $params = [];
        $types  = '';

        if (!empty($filters['from'])) {
            $conds[]  = "created_at >= ?";
            $params[] = $filters['from'];
            $types   .= 's';
        }
        if (!empty($filters['to'])) {
            $conds[]  = "created_at <= ?";
            $params[] = $filters['to'];
            $types   .= 's';
        }

        if ($conds) {
            $sql .= ' WHERE ' . implode(' AND ', $conds);
        }

        $sql .= ' GROUP BY DATE(created_at) ORDER BY fecha';

        $stmt = $this->database->prepare($sql);
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        if (method_exists($stmt, 'get_result')) {
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        $stmt->bind_result($fecha, $total);
        $rows = [];
        while ($stmt->fetch()) {
            $rows[] = [
                'fecha' => $fecha,
                'total' => (int)$total,
            ];
        }
        return $rows;
    }

    public function getCategories(): array {
        // tu wrapper devuelve ya un array asociativo:
        $rows = $this->database->query("SELECT id, name FROM categories");
        // si devuelves siempre un array, basta con devolverlo
        return $rows;
    }

    public function getQuestionsByDifficulty(int $categoryId): array
    {
        $sql = "
      SELECT difficulty, COUNT(*) AS total
        FROM questions
       WHERE category_id = ?
       GROUP BY difficulty
       ORDER BY difficulty
    ";
        $stmt = $this->database->prepare($sql);
        $stmt->bind_param('i', $categoryId);
        $stmt->execute();

        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }



    //-----------------------///-------------------------------//


    public function getPlayersByGender(): array {
        $sql = "
        SELECT g.type AS genero, COUNT(u.id) AS total
        FROM users u
        JOIN gender g ON u.id_gender = g.id
        WHERE u.id_rol = 3
        GROUP BY u.id_gender
    ";
        return $this->database->query($sql);
    }

    public function getPlayersByCountry(): array {
        $sql = "
        SELECT u.country AS pais, COUNT(u.id) AS total
        FROM users u
        WHERE u.id_rol = 3
        GROUP BY u.country
    ";
        return $this->database->query($sql);
    }

}