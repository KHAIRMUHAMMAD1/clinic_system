<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
$page_title = "Add Doctor";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $specialization = trim($_POST['specialization']);
    $phone = trim($_POST['phone']);

    if (!$full_name || !$specialization) {
        $errors[] = "Full Name and Specialization are required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO doctors (full_name, specialization, phone) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $full_name, $specialization, $phone);
        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<h2>Add Doctor</h2>

<?php if($errors): ?>
<div class="alert alert-danger">
  <?php foreach($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?>
</div>
<?php endif; ?>

<form method="post">
  <div class="mb-3">
    <label class="form-label">Full Name</label>
    <input type="text" name="full_name" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Specialization</label>
    <input type="text" name="specialization" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Phone</label>
    <input type="text" name="phone" class="form-control">
  </div>
  <button class="btn btn-primary">Save</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
