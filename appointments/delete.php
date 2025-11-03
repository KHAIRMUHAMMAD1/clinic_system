<?php
// appointments/delete.php
require_once __DIR__ . '/../includes/auth_check.php';

$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
header('Location: index.php');
exit;
