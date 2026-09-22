<?php
// signup.php - styled to match the elegant visual login page (uses the uploaded image)
// Requires: config.php (session + $pdo)
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
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?=htmlspecialchars($page_title)?> — Mess Management</title>

  <style>
    :root{
      --bg:#e6e0ff;
      --panel-bg:#ffffff;
      --muted:#6b7280;
      --purple-1:#6550f8;
      --purple-2:#4b3ce0;
      --btn-gradient: linear-gradient(90deg,var(--purple-1),var(--purple-2));
      --radius:18px;
      --card-padding:34px;
      --shadow: 0 24px 60px rgba(28,20,80,0.12);
      --max-width:1100px;
      --font-sans: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family:var(--font-sans);
      background:linear-gradient(180deg,#cfc7ff 0%, #e9e4ff 35%, #ffffff 100%);
      display:flex;
      align-items:center;
      justify-content:center;
      padding:32px;
      color:#0f172a;
    }

    .wrap{
      width:100%;
      max-width:var(--max-width);
      background:var(--panel-bg);
      border-radius:20px;
      box-shadow:var(--shadow);
      overflow:hidden;
      display:grid;
      grid-template-columns: 520px 1fr;
      gap:0;
      border: 8px solid rgba(255,255,255,0.7);
    }
    @media (max-width:1000px){
      .wrap{grid-template-columns:1fr; padding:0 0;}
      .left, .right-illustration{padding:22px}
      .illustration{max-width:100%}
    }

    /* Left form area (now on the left for signup) */
    .left {
      padding: var(--card-padding);
      display:flex;
      flex-direction:column;
      gap:18px;
    }

    .brand {
      display:flex;
      align-items:center;
      gap:12px;
      margin-bottom:6px;
    }
    .logo {
      width:48px;height:48px;border-radius:10px;background:linear-gradient(180deg,#fff,#f5f5ff);display:flex;align-items:center;justify-content:center;border:2px solid rgba(75,60,200,0.12);
      font-weight:800;color:var(--purple-2);font-size:20px;
    }
    .brand h3{margin:0;color:var(--purple-2);font-weight:800;font-size:1.1rem}
    .brand .tag{color:var(--muted);font-size:0.88rem}

    .hero-title{font-size:36px;line-height:1;margin:6px 0 0 0;font-weight:800}
    .hero-sub{color:var(--muted);font-size:15px;margin-top:6px}

    .form {
      margin-top:8px;
      display:flex;
      flex-direction:column;
      gap:14px;
    }

    .field {
      display:flex;
      flex-direction:column;
      gap:8px;
    }

    label.field-label{
      font-weight:600;color:#111827;font-size:0.95rem;
    }

    input[type="text"], input[type="email"], input[type="password"], select{
      height:52px;
      border-radius:14px;
      border:1.6px solid rgba(16,24,40,0.12);
      padding:12px 16px;
      font-size:15px;
      background:#fff;
      transition:box-shadow .14s ease, transform .08s ease, border-color .12s ease;
    }
    input:focus, select:focus{
      outline:none;
      box-shadow: 0 10px 30px rgba(75,60,200,0.08);
      border-color: rgba(75,60,200,0.35);
      transform:translateY(-1px);
    }

    .btn-sign{
      margin-top:4px;
      display:inline-block;
      width:100%;
      height:56px;
      border-radius:14px;
      border:none;
      color:white;
      font-weight:800;
      font-size:18px;
      cursor:pointer;
      background:var(--btn-gradient);
      box-shadow: 0 12px 28px rgba(75,60,200,0.18);
      transition:transform .12s ease, box-shadow .12s ease;
    }
    .btn-sign:hover{transform:translateY(-4px);box-shadow:0 20px 40px rgba(75,60,200,0.22)}

    .back-link{display:inline-block;padding:10px 14px;border-radius:12px;border:1px solid rgba(16,24,40,0.06);background:transparent;color:#111827;text-decoration:none;font-weight:700}

    .right-illustration{
      background:linear-gradient(180deg,#bfa9ff,#9f90ff 60%);
      padding:30px;border-left:1px solid rgba(255,255,255,0.05);display:flex;flex-direction:column;align-items:center;justify-content:center;
    }
    .right-inner {
      width:100%;height:100%;border-radius:14px;background:transparent;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:16px;color:white;
    }
    .illustration {
      width:100%;
      max-width:420px;
      height:auto;
      border-radius:12px;
      object-fit:cover;
      box-shadow:0 18px 50px rgba(10,10,40,0.12);
      border: 8px solid rgba(255,255,255,0.06);
    }
    .caption{
      margin-top:18px;font-size:16px;color:rgba(255,255,255,0.95);font-weight:600;text-align:center
    }

    .success-box{background:#ecfdf5;border-left:4px solid #10b981;padding:12px;border-radius:8px;color:#065f46;font-weight:700}
    .error-box{background:#fff1f2;border-left:4px solid #ef4444;padding:12px;border-radius:8px;color:#991b1b;font-weight:700}

    .hint{font-size:0.95rem;color:var(--muted)}
  </style>
</head>
<body>
  <div class="wrap" role="main" aria-labelledby="signup-heading">
    <!-- LEFT (form) -->
    <div class="left">
      <div class="brand">
        <div class="logo">MG</div>
        <div>
          <h3>Mess</h3>
          <div class="tag">Hostel Mess Portal</div>
        </div>
      </div>

      <div>
        <h1 id="signup-heading" class="hero-title">Create account</h1>
        <div class="hero-sub">Register a warden and associate them with a hostel.</div>
      </div>

      <?php if($success): ?>
        <div class="success-box" role="status">
          Account created successfully. <a href="login.php" style="color:var(--purple-2);font-weight:800;text-decoration:none">Login now</a>.
        </div>
      <?php endif; ?>

      <?php if($errors): ?>
        <div class="error-box" role="alert">
          <?php foreach($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
        </div>
      <?php endif; ?>

      <form method="post" class="form" novalidate>
        <div class="field">
          <label class="field-label" for="name">Full name</label>
          <input id="name" name="name" type="text" placeholder="e.g. A. Ahmed" required autocomplete="name" />
        </div>

        <div class="field">
          <label class="field-label" for="email">Email</label>
          <input id="email" name="email" type="email" placeholder="warden@example.edu" required autocomplete="email" />
        </div>

        <div class="field">
          <label class="field-label" for="password">Password</label>
          <input id="password" name="password" type="password" placeholder="Choose a strong password" required autocomplete="new-password" />
          <div class="hint">Use at least 8 characters including a number and a letter.</div>
        </div>

        <div class="field">
          <label class="field-label" for="hostel">Hostel</label>
          <select id="hostel" name="hostel" required>
            <option value="">Select hostel</option>
            <option value="BRJC">BRJC</option>
            <option value="APJ BLOCK A">APJ BLOCK A</option>
            <option value="BLOCK C">BLOCK C</option>
            <option value="Fatima Zehra">Fatima Zehra</option>
          </select>
        </div>

        <div style="display:flex;gap:12px;align-items:center;margin-top:8px">
          <button class="btn-sign" type="submit">Sign up</button>
          <a class="back-link" href="login.php">Back to login</a>
        </div>

      </form>
    </div>

    <!-- RIGHT (artwork) -->
    <!-- <div class="right-illustration" aria-hidden="true">
      <div class="right-inner">
        <img class="illustration" src="/mnt/data/Screenshot 2025-11-25 at 01.21.08.png" alt="art">
        <div class="caption">Manage your task in an easy and more efficient way with Mess...</div>
      </div> -->
    </div>
  </div>
</body>
</html>
