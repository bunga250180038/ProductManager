<?php

require_once "config/db.php";
require_once "config/csrf.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$csrf_token = $_POST["csrf_token"] ?? "";

if (!verify_csrf_token($csrf_token)) {
    http_response_code(403);
    die("Permintaan tidak valid.");
}

$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("
    DELETE FROM products
    WHERE id = :id
");

$stmt->execute([
    "id" => $id
]);

header("Location: index.php?status=deleted");
exit;
?>