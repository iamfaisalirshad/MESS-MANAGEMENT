<?php
// signup.php - create a warden account
require 'config.php';
$page_title = "Sign up";

$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $hostel = $_POST['hostel'] ?? '';

    if (!$name || !$email || !$password || !$hostel) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    } else {
        // check existing
        $stmt = $pdo->prepare("SELECT id FROM wardens WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO wardens (name,email,password,hostel) VALUES (?,?,?,?)");
            try {
                $stmt->execute([$name, $email, $hash, $hostel]);
                $success = true;
            } catch (Exception $e) {
                $errors[] = "Registration failed: " . $e->getMessage();
            }
        }
    }
}

require 'header.php';
?>
<div class="card" style="margin-top:18px;max-width:820px">
  <h1>Create warden account</h1>
  <p class="lead">Register a warden and associate them with a hostel.</p>

  <?php if($success): ?>
    <div class="notice" style="border-left:4px solid #10b981">
      Account created. <a href="login.php">Login now</a>.
    </div>
  <?php endif; ?>

  <?php if($errors): ?>
    <div class="notice" style="border-left:4px solid #ef4444;margin-top:12px">
      <?php foreach($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
    </div>
  <?php endif; ?>

  <form method="post" style="margin-top:14px">
    <div class="form-row" style="margin-bottom:12px">
      <div class="col">
        <label>Full name</label>
        <input type="text" name="name" placeholder="e.g. A. Ahmed" required>
      </div>
      <div class="col">
        <label>Email</label>
        <input type="email" name="email" placeholder="warden@example.edu" required>
      </div>
    </div>

    <div class="form-row" style="margin-bottom:12px">
      <div class="col">
        <label>Password</label>
        <input type="password" name="password" placeholder="Choose a strong password" required>
      </div>
      <div class="col">
        <label>Hostel</label>
        <select name="hostel" required>
          <option value="">Select hostel</option>
          <option value="BRJC">BRJC</option>
          <option value="APJ BLOCK A">APJ BLOCK A</option>
          <option value="BLOCK C">BLOCK C</option>
          <option value="Fatima Zehra">Fatima Zehra</option>
        </select>
      </div>
    </div>

    <div style="display:flex;gap:12px;margin-top:8px">
      <button class="btn btn-primary" type="submit">Sign up</button>
      <a class="btn btn-secondary" href="login.php">Back to login</a>
    </div>
  </form>
</div>

<?php require 'footer.php'; ?>
