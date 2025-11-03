<?php
// appointments/create.php

require_once __DIR__ . '/../includes/db.php';
$page_title = "Add Appointment";
include __DIR__ . '/../includes/header.php';

$errors = [];

// ✅ Fetch all patients and doctors for dropdowns
$patients = $conn->query("SELECT id, full_name FROM patients ORDER BY full_name");
$doctors  = $conn->query("SELECT id, full_name, specialization FROM doctors ORDER BY full_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = intval($_POST['patient_id'] ?? 0);
    $doctor_id  = intval($_POST['doctor_id'] ?? 0);
    $date       = trim($_POST['appointment_date'] ?? '');
    $time       = trim($_POST['appointment_time'] ?? '');
    $reason     = trim($_POST['reason'] ?? '');
    $status     = trim($_POST['status'] ?? 'Scheduled');

    if (!$patient_id) $errors[] = "Please select a patient.";
    if (!$doctor_id)  $errors[] = "Please select a doctor.";
    if ($date === '' || $time === '') $errors[] = "Date and time are required.";

    if (empty($errors)) {
        // Combine date and time for DATETIME column
        $appointment_datetime = $date . ' ' . $time;

        $stmt = $conn->prepare("
            INSERT INTO appointments 
            (patient_id, doctor_id, appointment_date, reason, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iisss", $patient_id, $doctor_id, $appointment_datetime, $reason, $status);

        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
}
?>

<div class="container mt-4">
  <h2>Add New Appointment</h2>

  <?php if($errors): ?>
    <div class="alert alert-danger">
      <?php foreach($errors as $e): ?>
        <div><?= htmlspecialchars($e) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post">

    <!-- Patient Dropdown -->
    <div class="mb-3">
      <label class="form-label">Patient</label>
      <select name="patient_id" class="form-select" required>
        <option value="">-- Select Patient --</option>
        <?php while($p = $patients->fetch_assoc()): ?>
          <option value="<?= $p['id'] ?>" <?= (isset($patient_id) && $patient_id == $p['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($p['full_name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <!-- Doctor Dropdown -->
    <div class="mb-3">
      <label class="form-label">Doctor</label>
      <select name="doctor_id" class="form-select" required>
        <option value="">-- Select Doctor --</option>
        <?php while($d = $doctors->fetch_assoc()): ?>
          <option value="<?= $d['id'] ?>" <?= (isset($doctor_id) && $doctor_id == $d['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($d['full_name']) ?> (<?= htmlspecialchars($d['specialization']) ?>)
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <!-- Date and Time -->
    <div class="row mb-3">
      <div class="col">
        <label class="form-label">Date</label>
        <input type="date" name="appointment_date" class="form-control" required>
      </div>
      <div class="col">
        <label class="form-label">Time</label>
        <input type="time" name="appointment_time" class="form-control" required>
      </div>
    </div>

    <!-- Reason -->
    <div class="mb-3">
      <label class="form-label">Reason</label>
      <textarea name="reason" class="form-control" rows="3"></textarea>
    </div>

    <!-- Status -->
    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="Scheduled">Scheduled</option>
        <option value="Completed">Completed</option>
        <option value="Cancelled">Cancelled</option>
      </select>
    </div>

    <button class="btn btn-primary">Save Appointment</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
