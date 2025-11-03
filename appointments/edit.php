<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
$page_title = "Edit Appointment";
include __DIR__ . '/../includes/header.php';

$id = intval($_GET['id'] ?? 0);
$errors = [];

// Fetch appointment record
$stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();

if (!$appointment) {
    echo "<div class='alert alert-danger'>Appointment not found.</div>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// Fetch lists for dropdowns
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
        $appointment_datetime = $date . ' ' . $time;

        $stmt = $conn->prepare("UPDATE appointments 
                                SET patient_id=?, doctor_id=?, appointment_date=?, reason=?, status=? 
                                WHERE id=?");
        $stmt->bind_param("iisssi", $patient_id, $doctor_id, $appointment_datetime, $reason, $status, $id);
        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
}
?>

<h2>Edit Appointment</h2>

<?php if($errors): ?>
<div class="alert alert-danger">
    <?php foreach($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?>
</div>
<?php endif; ?>

<form method="post">
  <div class="mb-3">
    <label class="form-label">Patient</label>
    <select name="patient_id" class="form-select" required>
      <option value="">-- Select patient --</option>
      <?php while($p = $patients->fetch_assoc()): ?>
        <option value="<?=$p['id']?>" <?=($appointment['patient_id']==$p['id'])?'selected':''?>>
          <?=htmlspecialchars($p['full_name'])?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Doctor</label>
    <select name="doctor_id" class="form-select" required>
      <option value="">-- Select doctor --</option>
      <?php while($d = $doctors->fetch_assoc()): ?>
        <option value="<?=$d['id']?>" <?=($appointment['doctor_id']==$d['id'])?'selected':''?>>
          <?=htmlspecialchars($d['full_name'])?> (<?=htmlspecialchars($d['specialization'])?>)
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <?php
  // Split datetime for form fields
  $datePart = date('Y-m-d', strtotime($appointment['appointment_date']));
  $timePart = date('H:i', strtotime($appointment['appointment_date']));
  ?>

  <div class="row mb-3">
    <div class="col">
      <label class="form-label">Date</label>
      <input type="date" name="appointment_date" class="form-control" value="<?=$datePart?>" required>
    </div>
    <div class="col">
      <label class="form-label">Time</label>
      <input type="time" name="appointment_time" class="form-control" value="<?=$timePart?>" required>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Reason</label>
    <textarea name="reason" class="form-control"><?=htmlspecialchars($appointment['reason'])?></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select">
      <option <?=($appointment['status']=='Scheduled')?'selected':''?>>Scheduled</option>
      <option <?=($appointment['status']=='Completed')?'selected':''?>>Completed</option>
      <option <?=($appointment['status']=='Cancelled')?'selected':''?>>Cancelled</option>
    </select>
  </div>

  <button class="btn btn-primary">Update</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
