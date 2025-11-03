<?php
// doctors/detail.php
require_once __DIR__ . '/../includes/auth_check.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { echo "Invalid doctor id."; exit; }

$stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$doctor = $stmt->get_result()->fetch_assoc();
if (!$doctor) { echo "Doctor not found."; exit; }

include __DIR__ . '/../includes/header.php';
?>

<h2>Doctor Details</h2>
<table class="table table-bordered">
  <tr><th>ID</th><td><?=htmlspecialchars($doctor['id'])?></td></tr>
  <tr><th>Full Name</th><td><?=htmlspecialchars($doctor['full_name'])?></td></tr>
  <tr><th>Specialization</th><td><?=htmlspecialchars($doctor['specialization'])?></td></tr>
  <tr><th>Phone</th><td><?=htmlspecialchars($doctor['phone'])?></td></tr>
  
</table>

<a href="index.php" class="btn btn-secondary">Back to Doctors</a>
<a href="edit.php?id=<?= $doctor['id'] ?>" class="btn btn-primary">Edit</a>
<a href="delete.php?id=<?= $doctor['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this doctor?')">Delete</a>

<?php include __DIR__ . '/../includes/footer.php'; ?>
