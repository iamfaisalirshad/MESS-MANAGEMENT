<?php
// add_boarder.php
require 'config.php';
ensure_logged_in();
$page_title = "Add Boarder";

$hostel = $_SESSION['warden_hostel'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $roll = trim($_POST['roll_number'] ?? '');
    $room = trim($_POST['room_number'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $branch = trim($_POST['branch'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $contact = trim($_POST['contact'] ?? '');

    if (!$name || !$roll || !$room || !$department || !$branch || !$year) {
        $errors[] = "Please fill all required fields.";
    }

    // photo upload (optional)
    $photoPath = null;
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = 'uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array(strtolower($ext), $allowed)) {
            $errors[] = "Invalid photo format. Use jpg/png/gif.";
        } else {
            $fname = $targetDir . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $fname)) {
                $errors[] = "Failed to upload photo.";
            } else {
                $photoPath = $fname;
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO boarders (name, roll_number, hostel, room_number, department, branch, year, contact, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$name, $roll, $hostel, $room, $department, $branch, $year, $contact, $photoPath]);
            header('Location: dashboard.php?added=1');
            exit;
        } catch (Exception $e) {
            $errors[] = "Failed to add boarder: " . $e->getMessage();
            if ($photoPath && file_exists($photoPath)) unlink($photoPath);
        }
    }
}

require 'header.php';
?>

<div class="card">
  <h1>Add Boarder</h1>
  <p class="lead">Add a new boarder for hostel <strong><?=htmlspecialchars($hostel)?></strong></p>

  <?php if($errors): ?>
    <div class="notice" style="border-left:4px solid #ef4444;margin-bottom:12px">
      <?php foreach($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" style="max-width:900px">
    <div class="form-row" style="margin-bottom:12px">
      <div class="col">
        <label>Full name *</label>
        <input type="text" name="name" required>
      </div>
      <div class="col">
        <label>Roll number *</label>
        <input type="text" name="roll_number" required>
      </div>
    </div>

    <div class="form-row" style="margin-bottom:12px">
      <div class="col">
        <label>Room number *</label>
        <input type="text" name="room_number" required>
      </div>
      <div class="col">
        <label>Department *</label>
        <input type="text" name="department" required>
      </div>
      <div class="col">
        <label>Branch *</label>
        <input type="text" name="branch" required>
      </div>
    </div>

    <div class="form-row" style="margin-bottom:12px">
      <div class="col">
        <label>Year *</label>
        <input type="text" name="year" placeholder="e.g. 3rd" required>
      </div>
      <div class="col">
        <label>Contact</label>
        <input type="text" name="contact" placeholder="+91 9XXXXXXXXX">
      </div>
      <div class="col">
        <label>Photo (optional)</label>
        <input type="file" name="photo" accept=".jpg,.jpeg,.png,.gif">
      </div>
    </div>

    <div style="display:flex;gap:10px">
      <button class="btn btn-primary" type="submit">Save boarder</button>
      <a class="btn btn-secondary" href="dashboard.php">Cancel</a>
    </div>
  </form>
</div>

<?php require 'footer.php'; ?>
