<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$doctor = $stmt->get_result()->fetch_assoc();

if (!$doctor) {
    header('Location: index.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $specialization = trim($_POST['specialization']);
    $phone = trim($_POST['phone']);

    if (!$full_name || !$specialization) {
        $errors[] = "Full Name and Specialization are required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE doctors SET full_name=?, specialization=?, phone=? WHERE id=?");
        $stmt->bind_param("sssi", $full_name, $specialization, $phone, $id);
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

<h2>Edit Doctor</h2>

<?php if($errors): ?>
<div class="alert alert-danger">
  <?php foreach($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?>
</div>
<?php endif; ?>

<form method="post">
  <div class="mb-3">
    <label class="form-label">Full Name</label>
    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($doctor['full_name']) ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Specialization</label>
    <input type="text" name="specialization" class="form-control" value="<?= htmlspecialchars($doctor['specialization']) ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Phone</label>
    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($doctor['phone']) ?>">
  </div>
  <button class="btn btn-primary">Update</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
