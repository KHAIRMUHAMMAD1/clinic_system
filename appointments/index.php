<?php
// appointments/index.php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
$page_title = "Appointments";
include __DIR__ . '/../includes/header.php';

// Fetch appointments with JOINs
$query = "
    SELECT a.id, a.appointment_date, a.reason, a.status,
           p.full_name AS patient_name,
           d.full_name AS doctor_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    ORDER BY a.appointment_date DESC
";
$result = $conn->query($query);
?>
<div class="container mt-4">
  <h2>Appointments</h2>
  <a href="create.php" class="btn btn-success mb-3">+ Add Appointment</a>

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Patient</th>
        <th>Doctor</th>
        <th>Date & Time</th>
        <th>Reason</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dt = $row['appointment_date'] ? date('Y-m-d H:i', strtotime($row['appointment_date'])) : '';
        ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['patient_name']) ?></td>
          <td><?= htmlspecialchars($row['doctor_name']) ?></td>
          <td><?= htmlspecialchars($dt) ?></td>
          <td><?= htmlspecialchars($row['reason']) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td>
            <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Details</a>
            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
               onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
          </td>
        </tr>
        <?php
    }
} else {
    echo '<tr><td colspan="7" class="text-center">No appointments found.</td></tr>';
}
?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
