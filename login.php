<?php
// login.php - visual clone of provided design (uses your uploaded image)
// Requires: config.php (session + $pdo)
require 'config.php';
$page_title = "Warden Login";

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $errors[] = "Email and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM wardens WHERE email = ?");
        $stmt->execute([$email]);
        $warden = $stmt->fetch();
        if ($warden && password_verify($password, $warden['password'])) {
            // set session
            $_SESSION['warden_id'] = $warden['id'];
            $_SESSION['warden_name'] = $warden['name'];
            $_SESSION['warden_hostel'] = $warden['hostel'];
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = "Invalid email or password.";
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
    /* Base tokens */
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

    /* Container */
    .wrap{
      width:100%;
      max-width:var(--max-width);
      background:var(--panel-bg);
      border-radius:20px;
      box-shadow:var(--shadow);
      overflow:hidden;
      display:grid;
      grid-template-columns: 1fr 520px;
      gap:0;
      border: 8px solid rgba(255,255,255,0.7);
    }
    @media (max-width:1000px){
      .wrap{grid-template-columns:1fr; padding:0 0;}
      .right-illustration{order:-1;height:260px}
    }

    /* Left form area */
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

    .hero-title{font-size:40px;line-height:1;margin:6px 0 0 0;font-weight:800}
    .hero-sub{color:var(--muted);font-size:15px;margin-top:6px}

    /* form fields */
    .form {
      margin-top:8px;
      display:flex;
      flex-direction:column;
      gap:16px;
    }

    .field {
      display:flex;
      flex-direction:column;
      gap:8px;
    }

    label.field-label{
      font-weight:600;color:#111827;font-size:0.95rem;
      position:relative;
    }

    input[type="email"], input[type="password"]{
      height:56px;
      border-radius:14px;
      border:1.6px solid rgba(16,24,40,0.12);
      padding:14px 18px;
      font-size:15px;
      background:#fff;
      transition:box-shadow .14s ease, transform .08s ease, border-color .12s ease;
    }
    input:focus{
      outline:none;
      box-shadow: 0 10px 30px rgba(75,60,200,0.08);
      border-color: rgba(75,60,200,0.35);
      transform:translateY(-1px);
    }

    .forgot{
      text-align:right;font-size:14px;color:var(--muted);text-decoration:none;margin-top:-6px;
    }
    .forgot:hover{text-decoration:underline}

    /* Sign in button */
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

    /* divider */
    .divider {
      display:flex;align-items:center;gap:12px;margin-top:8px;
    }
    .divider:before, .divider:after {content:"";flex:1;height:1px;background:linear-gradient(90deg,transparent,#e6e6e6,transparent)}
    .divider span{font-size:14px;color:var(--muted);padding:0 8px}

    /* google button */
    .btn-google{
      display:flex;align-items:center;gap:12px;justify-content:center;
      height:54px;border-radius:12px;border:1px solid rgba(16,24,40,0.06);
      background:white;color:#111827;font-weight:700;cursor:pointer;
      transition:transform .12s ease,box-shadow .12s ease;
    }
    .btn-google:hover{transform:translateY(-3px);box-shadow:0 10px 20px rgba(16,24,40,0.06)}

    .signup-line{color:var(--muted);text-align:center;padding:12px 0;font-size:15px}
    .signup-line a{color:var(--purple-2);font-weight:700;text-decoration:none}
    .signup-line a:hover{text-decoration:underline}

    /* right illustration panel */
    .right-illustration{
      background:linear-gradient(180deg,#bfa9ff,#9f90ff 60%);
      padding:30px;border-left:1px solid rgba(255,255,255,0.05);display:flex;flex-direction:column;align-items:center;justify-content:space-between;
      position:relative;
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
    .dots {
      display:flex;gap:8px;margin-top:14px;
    }
    .dot{width:10px;height:10px;border-radius:999px;background:rgba(255,255,255,0.6)}
    .dot.active{background:white;box-shadow:0 6px 18px rgba(0,0,0,0.08)}

    /* error box */
    .error-box{background:#fff1f2;border-left:4px solid #ef4444;padding:10px;border-radius:8px;color:#991b1b;font-weight:700}

    /* small responsive tweaks */
    @media (max-width:700px){
      .hero-title{font-size:28px}
      .wrap{grid-template-columns:1fr}
      .right-illustration{display:block;height:260px}
    }
  </style>
</head>
<body>
  <div class="wrap" role="main" aria-labelledby="welcome-heading">
    <!-- LEFT -->
    <div class="left">
      <div class="brand">
        <div class="logo">MG</div>
        <div>
          <h3>Mess</h3>
          <div class="tag">Hostel Mess Portal</div>
        </div>
      </div>

      <div>
        <h1 id="welcome-heading" class="hero-title">Welcome Back!</h1>
        <div class="hero-sub">Please enter login details below</div>
      </div>

      <?php if($errors): ?>
        <div class="error-box" role="alert">
          <?php foreach($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
        </div>
      <?php endif; ?>

      <form method="post" class="form" novalidate>
        <div class="field">
          <label class="field-label" for="email">Email</label>
          <input id="email" name="email" type="email" placeholder="Enter the email" required autocomplete="email" />
        </div>

        <div class="field">
          <label class="field-label" for="password">Password</label>
          <input id="password" name="password" type="password" placeholder="Enter the Password" required autocomplete="current-password" />
        </div>

        <div>
          <a class="forgot" href="#" onclick="alert('Contact admin to reset password');return false;">Forgot password?</a>
        </div>

        <button type="submit" class="btn-sign">Sign in</button>

        <div class="divider"><span>Or continue</span></div>

        <button type="button" class="btn-google" onclick="alert('Google login not wired.');">
          <svg style="width:20px;height:20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M21.6 12.227c0-.72-.064-1.41-.183-2.073H12v3.94h5.36c-.23 1.33-1.11 2.45-2.37 3.19v2.655h3.82c2.234-2.06 3.45-5.09 3.45-8.712z" fill="#4285F4"/>
            <path d="M12 22c2.97 0 5.465-.98 7.287-2.656l-3.82-2.654c-1.06.71-2.42 1.14-3.468 1.14-2.667 0-4.93-1.8-5.737-4.22H2.198v2.65C3.99 19.98 7.74 22 12 22z" fill="#34A853"/>
            <path d="M6.263 13.61A6.997 6.997 0 0 1 5.6 12c0-.67.105-1.317.297-1.61V7.74H2.198A10.997 10.997 0 0 0 1 12c0 1.77.37 3.45 1.198 4.892l3.065-3.282z" fill="#FBBC05"/>
            <path d="M12 6.5c1.61 0 3.06.56 4.2 1.66l3.14-3.14C17.455 3.06 14.97 2 12 2 7.74 2 3.99 4.02 2.198 7.74L5.6 9.9C6.07 8.3 8.333 6.5 12 6.5z" fill="#EA4335"/>
          </svg>
          Log in with Google
        </button>

        <div class="signup-line">Don't have an account? <a href="signup.php">Sign Up</a></div>
      </form>
    </div>

    <!-- RIGHT (uses uploaded image) -->
    <!-- <div class="right-illustration" aria-hidden="true"> -->
      <!-- <div class="right-inner">
        <img class="illustration" src="/mnt/data/Screenshot 2025-11-25 at 01.21.08.png" alt="right art">
        <div class="caption">Manage your task in a easy and more efficient way with Mess...</div>
        <div class="dots"><div class="dot"></div><div class="dot active"></div><div class="dot"></div></div>
      </div> -->
    </div>
  </div>
</body>
</html>
