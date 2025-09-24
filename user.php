<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

$host = 'localhost';
$db = 'my_database';
$user = 'root';
$password = '';
$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['pesquisa'])) {
            $pesquisa = "%" . $_GET['pesquisa'] . "%";
            $stmt = $conn->prepare("SELECT * FROM users WHERE login LIKE ? OR name LIKE ?");
            $stmt->bind_param("ss", $pesquisa, $pesquisa);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query("SELECT * FROM users ORDER BY id DESC");
        }

        $retorno = [];
        while ($linha = $result->fetch_assoc()) {
            $retorno[] = $linha;
        }
        echo json_encode($retorno);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $conn->prepare("INSERT INTO users (login, name, email, password, active) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $data['login'], $data['name'], $data['email'], $data['password'], $data['active']);
        $stmt->execute();
        echo json_encode(['status' => 'ok', 'insert_id' => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $conn->prepare("UPDATE users SET login=?, name=?, email=?, password=?, active=? WHERE id=?");
        $stmt->bind_param("ssssii", $data['login'], $data['name'], $data['email'], $data['password'], $data['active'], $data['id']);
        $stmt->execute();
        echo json_encode(['status' => 'ok']);
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $_GET['id']);
            $stmt->execute();
            echo json_encode(['status' => 'ok']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID not provided']);
        }
        break;
}

?>