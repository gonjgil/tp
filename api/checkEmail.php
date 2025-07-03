<?php
header('Content-Type: application/json');

require_once '../core/Database.php';

$db = new Database('localhost', 'root', 'trivia', '');
$conn = $db->getConnection();

if (!isset($_GET['email'])) {
    echo json_encode(['error' => 'Falta el parámetro email']);
    exit;
}

$email = $_GET['email'];

$stmt = $conn->prepare('SELECT id, name FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $name);

if ($stmt->num_rows > 0 && $stmt->fetch()) {
    echo json_encode([
        'available' => false,
        'id' => $id,
        'name' => $name
    ]);
} else {
    echo json_encode(['available' => true]);
}

$stmt->close();

