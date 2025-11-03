<?php
// patients/detail.php
require_once __DIR__ . '/../includes/auth_check.php'; // starts session and sets $conn

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "Invalid patient id.";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
if (!$patient) {
    echo "Patient not found.";
    exit;
}
include __DIR__ . '/../includes/header.php';
?>

<h2>Patient Details</h2>
<table class="table table-bordered">
  <tr><th>ID</th><td><?=htmlspecialchars($patient['id'])?></td></tr>
  <tr><th>Full Name</th><td><?=htmlspecialchars($patient['full_name'])?></td></tr>
  <tr><th>Date of Birth</th><td><?=htmlspecialchars($patient['dob'])?></td></tr>
  <tr><th>Gender</th><td><?=htmlspecialchars($patient['gender'])?></td></tr>
  <tr><th>Phone</th><td><?=htmlspecialchars($patient['phone'])?></td></tr>
  <tr><th>Email</th><td><?=htmlspecialchars($patient['email'])?></td></tr>
  <tr><th>Address</th><td><?=nl2br(htmlspecialchars($patient['address']))?></td></tr>
  <tr><th>Created At</th><td><?=htmlspecialchars($patient['created_at'])?></td></tr>
</table>

<a href="index.php" class="btn btn-secondary">Back to Patients</a>
<a href="edit.php?id=<?= $patient['id'] ?>" class="btn btn-primary">Edit</a>
<a href="delete.php?id=<?= $patient['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this patient?')">Delete</a>

<?php include __DIR__ . '/../includes/footer.php'; ?>
