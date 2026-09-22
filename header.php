<?php
// header.php - include at top of pages to get styles and nav
if (!isset($page_title)) $page_title = "Mess Management";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?=htmlspecialchars($page_title)?> — Mess Management</title>
  <style>
    /* ---------- Elegant built-in UI styles ---------- */
    :root{
      --bg:#f5f7fb;
      --card:#ffffff;
      --muted:#6b7280;
      --accent:#0b69ff;
      --accent-2:#06b6d4;
      --radius:12px;
      --glass: rgba(255,255,255,0.7);
      --shadow: 0 10px 30px rgba(15,23,42,0.08);
      --maxwidth:1100px;
      --fs-base:16px;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: linear-gradient(180deg,var(--bg),#ffffff 40%);
      color:#111827;
      font-size:var(--fs-base);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }
    .wrap{max-width:var(--maxwidth);margin:28px auto;padding:18px}
    header.appbar{
      background:linear-gradient(90deg,var(--accent), #0a58ca);
      color:white;padding:18px;border-radius:14px;box-shadow:var(--shadow);display:flex;align-items:center;justify-content:space-between;
    }
    .brand{font-weight:700;font-size:1.15rem;letter-spacing:0.2px}
    .brand-sub{font-size:0.85rem;opacity:0.95}
    nav .nav-actions a{color:white;text-decoration:none;margin-left:12px;font-weight:600;padding:8px 12px;border-radius:10px;display:inline-block}
    nav .nav-actions a.btn-ghost{background: rgba(255,255,255,0.12)}
    nav .nav-actions a.btn-white{background:white;color:var(--accent);}

    /* page layout */
    .grid{display:grid;grid-template-columns:1fr 320px;gap:18px;margin-top:18px}
    @media (max-width:980px){ .grid{grid-template-columns:1fr} }

    /* cards */
    .card{background:var(--card);border-radius:var(--radius);padding:16px;box-shadow:var(--shadow);border:1px solid rgba(11,85,230,0.04)}
    h1{margin:0 0 6px 0;font-size:1.5rem}
    h2{margin:0 0 12px 0;font-size:1.05rem}
    .lead{color:var(--muted);font-size:0.96rem;margin:6px 0 12px 0}

    /* forms */
    label{display:block;font-weight:600;margin-bottom:6px}
    .form-row{display:flex;gap:12px}
    .form-row .col{flex:1}
    input[type="text"], input[type="email"], input[type="password"], select, input[type="file"] , textarea{
      width:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(11,85,230,0.08);background: #fbfdff;
      font-size:0.95rem;
    }
    input:focus, select:focus, textarea:focus{outline:none;box-shadow:0 6px 20px rgba(11,85,230,0.06);border-color:var(--accent)}

    .muted{color:var(--muted);font-size:0.92rem}
    .btn{
      display:inline-block;padding:10px 14px;border-radius:10px;font-weight:700;border:none;cursor:pointer;
    }
    .btn-primary{background:linear-gradient(90deg,var(--accent), #0a58ca);color:white;box-shadow:0 10px 25px rgba(11,85,230,0.12)}
    .btn-secondary{background:transparent;border:1px solid rgba(0,0,0,0.06);color:#374151}

    /* table */
    .table{width:100%;border-collapse:collapse;margin-top:8px}
    .table th{background:#fbfdff;padding:12px;border-bottom:2px solid rgba(0,0,0,0.05);text-align:left;font-weight:700}
    .table td{padding:10px;border-bottom:1px solid rgba(15,23,42,0.04);vertical-align:middle}
    .avatar{width:56px;height:56px;border-radius:10px;object-fit:cover;display:inline-block}

    /* small helpers */
    .actions .btn{padding:6px 8px;font-size:0.9rem;border-radius:8px}
    .actions .btn-danger{background:#ef4444;color:white}
    .actions .btn-edit{background:#0b69ff;color:white}
    .text-right{text-align:right}
    .top-row{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:12px}
    @media (max-width:640px){ .top-row{flex-direction:column;align-items:stretch} }
    .notice{background:linear-gradient(90deg,#f8fafc,#ffffff);padding:10px;border-radius:10px;border:1px solid rgba(0,0,0,0.03);color:var(--muted)}
    .small{font-size:0.9rem;color:var(--muted)}
    .footer-note{margin-top:18px;text-align:center;color:var(--muted);font-size:0.9rem}
  </style>
</head>
<body>
  <div class="wrap">
    <header class="appbar">
      <div>
        <div class="brand">Mess Management</div>
        <div class="brand-sub">BRJC · APJ BLOCK A · BLOCK C · Fatima Zehra</div>
      </div>
      <nav class="nav-actions">
        <?php if(isset($_SESSION['warden_name'])): ?>
          <span class="small" style="color:rgba(255,255,255,0.95);margin-right:12px">Hello, <?=htmlspecialchars($_SESSION['warden_name'])?></span>
          <a href="dashboard.php" class="btn btn-ghost">Dashboard</a>
          <a href="logout.php" class="btn btn-ghost">Logout</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-white">Login</a>
          <a href="signup.php" class="btn btn-ghost">Sign up</a>
        <?php endif; ?>
      </nav>
    </header>
    <!-- main content begins after includes -->
