<?php
// appointments/detail.php
require_once __DIR__ . '/../includes/auth_check.php';
include __DIR__ . '/../includes/header.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "<div class='alert alert-danger'>Invalid appointment ID.</div>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$sql = "SELECT a.*, p.full_name AS patient_name, d.full_name AS doctor_name, d.specialization AS doctor_specialization
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$appointment = $res->fetch_assoc();
if (!$appointment) {
    echo "<div class='alert alert-danger'>Appointment not found.</div>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}
?>

<h2>Appointment Details</h2>
<table class="table table-bordered">
  <tr><th>ID</th><td><?=htmlspecialchars($appointment['id'])?></td></tr>
  <tr><th>Patient</th><td><?=htmlspecialchars($appointment['patient_name'])?></td></tr>
  <tr><th>Doctor</th><td><?=htmlspecialchars($appointment['doctor_name'])?> (<?=htmlspecialchars($appointment['doctor_specialization'])?>)</td></tr>
  <tr><th>Date & Time</th><td><?=htmlspecialchars(date('d M Y, h:i A', strtotime($appointment['appointment_date'])))?></td></tr>
  <tr><th>Reason</th><td><?=nl2br(htmlspecialchars($appointment['reason']))?></td></tr>
  <tr><th>Status</th><td><?=htmlspecialchars($appointment['status'])?></td></tr>
  
</table>

<a href="index.php" class="btn btn-secondary">Back to Appointments</a>
<a href="edit.php?id=<?= $appointment['id'] ?>" class="btn btn-primary">Edit</a>
<a href="delete.php?id=<?= $appointment['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this appointment?')">Delete</a>

<?php include __DIR__ . '/../includes/footer.php'; ?>
