<?php
require_once __DIR__ . '/../includes/auth_check.php';
include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Patients</h2>
  <a href="create.php" class="btn btn-success">Add Patient</a>
</div>

<?php
$result = $conn->query("SELECT * FROM patients ORDER BY full_name");
?>

<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Full Name</th>
      <th>Age</th>
      <th>Gender</th>
      <th>Phone</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['age']) ?></td>
        <td><?= htmlspecialchars($row['gender']) ?></td>
        <td><?= htmlspecialchars($row['phone']) ?></td>
        <td>
           <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Details</a>
          <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
          <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this patient?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include __DIR__ . '/../includes/footer.php'; ?>
