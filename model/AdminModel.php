<?php
class AdminModel {
    /** @var mysqli */
    private $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    /**
     * Devuelve un array asociativo con el total de preguntas por categoría,
     * acotado por un rango de fechas opcional.
     *
     * @param array $filters ['from' => 'YYYY-MM-DD', 'to' => 'YYYY-MM-DD']
     * @return array<int,array{category:string,total:int}>
     */
    public function getQuestionsByCategory(array $filters) {
        // 1) Base de la consulta
        $sql = "
          SELECT c.name   AS category,
                 COUNT(q.id) AS total
            FROM categories c
            LEFT JOIN questions q
              ON q.category_id = c.id
        ";

        // 2) Construimos cláusulas dinámicas y parámetros
        $conds  = [];
        $params = [];
        $types  = '';

        if (!empty($filters['from'])) {
            $conds[]  = "q.created_at >= ?";
            $params[] = $filters['from'];
            $types   .= 's';
        }
        if (!empty($filters['to'])) {
            $conds[]  = "q.created_at <= ?";
            $params[] = $filters['to'];
            $types   .= 's';
        }
        if ($conds) {
            $sql .= ' WHERE ' . implode(' AND ', $conds);
        }

        $sql .= ' GROUP BY c.id, c.name';

        // 3) Preparamos y bind de parámetros
        $stmt = $this->database->prepare($sql);
        if ($types !== '') {
            // bind_param espera lista de argumentos: tipo, &param1, &param2, …
            $stmt->bind_param($types, ...$params);
        }

        // 4) Ejecutamos
        $stmt->execute();

        // 5) Intentamos usar get_result()
        if (method_exists($stmt, 'get_result')) {
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        // 6) Fallback sin get_result(): bind_result + fetch()
        $stmt->bind_result($category, $total);
        $rows = [];
        while ($stmt->fetch()) {
            $rows[] = [
                'category' => $category,
                'total'    => (int)$total,
            ];
        }
        return $rows;
    }

    public function getQuestionsPerDay(array $filters): array
    {
        // 1) Base de la consulta
        $sql = "
      SELECT DATE(created_at) AS fecha,
             COUNT(id)         AS total
        FROM questions
    ";

        // 2) Condiciones dinámicas
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

        // 3) Agrupamos y ordenamos por fecha
        $sql .= ' GROUP BY DATE(created_at) ORDER BY fecha';

        // 4) Preparamos y linkamos parámetros
        $stmt = $this->database->prepare($sql);
        if ($types !== '') {
            // bind_param necesita tipo + referencias a cada valor
            $stmt->bind_param($types, ...$params);
        }

        // 5) Ejecutamos
        $stmt->execute();

        // 6) Intentamos fetch_all (si tu PHP lo soporta)
        if (method_exists($stmt, 'get_result')) {
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        // 7) Fallback para versiones sin get_result()
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
    public function getPlayersByDifficulty($difficulty = null) { /* consulta */ }

}