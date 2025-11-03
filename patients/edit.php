<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';

$id = intval($_GET['id'] ?? 0);

// Fetch patient record
$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

if (!$patient) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $dob = $_POST['dob'] ?? null;
    $gender = trim($_POST['gender']);
    $age = intval($_POST['age'] ?? 0);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    if (!$full_name || !$gender) {
        $errors[] = "Full Name and Gender are required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE patients 
                                SET full_name=?, dob=?, gender=?, age=?, phone=?, email=?, address=? 
                                WHERE id=?");
        $stmt->bind_param("sssisssi", 
            $full_name, $dob, $gender, $age, $phone, $email, $address, $id
        );

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

<h2>Edit Patient</h2>

<?php if($errors): ?>
<div class="alert alert-danger">
  <?php foreach($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?>
</div>
<?php endif; ?>

<form method="post">
  <div class="mb-3">
    <label class="form-label">Full Name</label>
    <input type="text" name="full_name" class="form-control" 
           value="<?= htmlspecialchars($patient['full_name']) ?>" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Date of Birth</label>
    <input type="date" name="dob" class="form-control" 
           value="<?= htmlspecialchars($patient['dob'] ?? '') ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Age</label>
    <input type="number" name="age" class="form-control" 
           value="<?= htmlspecialchars($patient['age'] ?? '') ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Gender</label>
    <select name="gender" class="form-select" required>
      <option value="Male" <?= ($patient['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
      <option value="Female" <?= ($patient['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
      <option value="Other" <?= ($patient['gender'] === 'Other') ? 'selected' : '' ?>>Other</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Phone</label>
    <input type="text" name="phone" class="form-control" 
           value="<?= htmlspecialchars($patient['phone'] ?? '') ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" 
           value="<?= htmlspecialchars($patient['email'] ?? '') ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Address</label>
    <textarea name="address" class="form-control"><?= htmlspecialchars($patient['address'] ?? '') ?></textarea>
  </div>

  <button class="btn btn-primary">Update</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
