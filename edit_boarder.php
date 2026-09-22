<?php
// edit_boarder.php
require 'config.php';
ensure_logged_in();
$page_title = "Edit Boarder";

$hostel = $_SESSION['warden_hostel'];
$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM boarders WHERE id = ? AND hostel = ?");
$stmt->execute([$id, $hostel]);
$boarder = $stmt->fetch();
if (!$boarder) {
    die("Boarder not found or access denied.");
}

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

    $photoPath = $boarder['photo'];
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = 'uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array(strtolower($ext), $allowed)) {
            $errors[] = "Invalid photo format.";
        } else {
            $fname = $targetDir . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $fname)) {
                $errors[] = "Failed to upload photo.";
            } else {
                if ($boarder['photo'] && file_exists($boarder['photo'])) unlink($boarder['photo']);
                $photoPath = $fname;
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE boarders SET name=?, roll_number=?, room_number=?, department=?, branch=?, year=?, contact=?, photo=? WHERE id=? AND hostel=?");
        try {
            $stmt->execute([$name, $roll, $room, $department, $branch, $year, $contact, $photoPath, $id, $hostel]);
            header('Location: dashboard.php?updated=1');
            exit;
        } catch (Exception $e) {
            $errors[] = "Update failed: " . $e->getMessage();
        }
    }
}

require 'header.php';
?>

<div class="card">
  <h1>Edit boarder</h1>
  <p class="lead">Update details for <strong><?=htmlspecialchars($boarder['name'])?></strong></p>

  <?php if($errors): ?>
    <div class="notice" style="border-left:4px solid #ef4444;margin-bottom:12px">
      <?php foreach($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" style="max-width:900px">
    <div class="form-row" style="margin-bottom:12px">
      <div class="col">
        <label>Full name *</label>
        <input type="text" name="name" value="<?=htmlspecialchars($boarder['name'])?>" required>
      </div>
      <div class="col">
        <label>Roll number *</label>
        <input type="text" name="roll_number" value="<?=htmlspecialchars($boarder['roll_number'])?>" required>
      </div>
    </div>

    <div class="form-row" style="margin-bottom:12px">
      <div class="col">
        <label>Room number *</label>
        <input type="text" name="room_number" value="<?=htmlspecialchars($boarder['room_number'])?>" required>
      </div>
      <div class="col">
        <label>Department *</label>
        <input type="text" name="department" value="<?=htmlspecialchars($boarder['department'])?>" required>
      </div>
      <div class="col">
        <label>Branch *</label>
        <input type="text" name="branch" value="<?=htmlspecialchars($boarder['branch'])?>" required>
      </div>
    </div>

    <div class="form-row" style="margin-bottom:12px">
      <div class="col">
        <label>Year *</label>
        <input type="text" name="year" value="<?=htmlspecialchars($boarder['year'])?>" required>
      </div>
      <div class="col">
        <label>Contact</label>
        <input type="text" name="contact" value="<?=htmlspecialchars($boarder['contact'])?>" >
      </div>
      <div class="col">
        <label>Replace photo</label>
        <input type="file" name="photo" accept=".jpg,.jpeg,.png,.gif">
      </div>
    </div>

    <div style="display:flex;gap:10px">
      <button class="btn btn-primary" type="submit">Update</button>
      <a class="btn btn-secondary" href="dashboard.php">Cancel</a>
    </div>
  </form>
</div>

<?php require 'footer.php'; ?>
