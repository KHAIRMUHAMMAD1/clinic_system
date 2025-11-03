<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $dob = $_POST['dob'] ?? null;
    $age = intval($_POST['age'] ?? 0);
    $gender = trim($_POST['gender']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    // Basic validation
    if (!$full_name || !$gender) {
        $errors[] = "Full Name and Gender are required.";
    }

    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    // If no errors, insert record
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO patients (full_name, dob, age, gender, phone, email, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissss", $full_name, $dob, $age, $gender, $phone, $email, $address);

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

<h2>Add New Patient</h2>

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
    <label class="form-label">Date of Birth</label>
    <input type="date" name="dob" class="form-control">
  </div>

  <div class="mb-3">
    <label class="form-label">Age</label>
    <input type="number" name="age" class="form-control">
  </div>

  <div class="mb-3">
    <label class="form-label">Gender</label>
    <select name="gender" class="form-select" required>
      <option value="">-- Select Gender --</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
      <option value="Other">Other</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Phone</label>
    <input type="text" name="phone" class="form-control">
  </div>

  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control">
  </div>

  <div class="mb-3">
    <label class="form-label">Address</label>
    <textarea name="address" class="form-control"></textarea>
  </div>

  <button class="btn btn-primary">Save</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
