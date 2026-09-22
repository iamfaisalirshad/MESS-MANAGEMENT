<?php
// dashboard.php (elegant neutral UI)
// Requires config.php, header.php, footer.php from your project.
require 'config.php';
ensure_logged_in();
$page_title = "Dashboard";

$hostel = $_SESSION['warden_hostel'];

// Basic search/filter (GET)
$q = trim($_GET['q'] ?? '');
$filter_year = trim($_GET['year'] ?? '');

// Build query with simple filters (prepared)
$sql = "SELECT * FROM boarders WHERE hostel = ?";
$params = [$hostel];
if ($q !== '') {
    $sql .= " AND (name LIKE ? OR roll_number LIKE ? OR room_number LIKE ? OR department LIKE ? OR branch LIKE ?)";
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like, $like);
}
if ($filter_year !== '') {
    $sql .= " AND year = ?";
    $params[] = $filter_year;
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$boarders = $stmt->fetchAll();

require 'header.php';
?>

<!-- Elegant neutral UI overrides for dashboard -->
<style>
  /* Neutral design tokens */
  :root{
    --bg-surface: #f6f7f8;
    --card-bg: #ffffff;
    --muted: #6b7280;
    --text: #111827;
    --accent: #374151; /* subtle dark-slate accent */
    --radius: 12px;
    --shadow-sm: 0 6px 18px rgba(15,23,42,0.06);
    --max-width: 1100px;
  }

  /* layout narrow */
  .dashboard-wrap{max-width:var(--max-width);margin:20px auto;padding:0 12px}
  .panel{background:var(--card-bg);border-radius:var(--radius);padding:18px;border:1px solid rgba(15,23,42,0.04);box-shadow:var(--shadow-sm)}
  .muted {color:var(--muted)}
  h1{font-size:1.4rem;margin:0 0 6px 0;color:var(--text);font-weight:700}
  .subtle {font-size:0.95rem;color:var(--muted);margin-bottom:12px}

  /* top controls */
  .controls{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-bottom:16px}
  .controls-left{display:flex;gap:12px;align-items:center}
  .search-input{background:#fbfdff;border:1px solid rgba(15,23,42,0.04);padding:10px 12px;border-radius:10px;width:360px;max-width:60ch}
  .select-input{padding:10px 12px;border-radius:10px;border:1px solid rgba(15,23,42,0.04);background:#fbfdff}
  .btn-subtle{background:transparent;border:1px solid rgba(15,23,42,0.06);padding:9px 12px;border-radius:10px;color:var(--accent);font-weight:600;cursor:pointer}
  .btn-primary{background:var(--accent);color:white;padding:10px 14px;border-radius:10px;border:none;font-weight:700;cursor:pointer;box-shadow:0 8px 20px rgba(55,65,81,0.08)}
  .view-toggle{display:flex;gap:8px;align-items:center}

  /* table */
  .table-clean{width:100%;border-collapse:collapse;margin-top:6px}
  .table-clean thead th{font-weight:700;text-align:left;padding:12px 10px;border-bottom:1px solid rgba(15,23,42,0.06);color:var(--muted);font-size:0.95rem}
  .table-clean tbody td{padding:12px 10px;border-bottom:1px solid rgba(15,23,42,0.03);vertical-align:middle;color:var(--text)}
  .table-clean tbody tr:hover td{background:linear-gradient(90deg,#ffffff,#fbfbfd)}
  .tiny {font-size:0.9rem;color:var(--muted)}

  /* avatar */
  .avatar{width:56px;height:56px;border-radius:10px;object-fit:cover;display:inline-block;border:1px solid rgba(15,23,42,0.03)}

  /* actions */
  .actions{display:flex;gap:8px}
  .action-edit{background:#eef2ff;border-radius:8px;padding:7px 9px;border:none;color:#1f2937;font-weight:700;cursor:pointer}
  .action-del{background:transparent;border:1px solid rgba(15,23,42,0.06);padding:7px 9px;border-radius:8px;color:#b91c1c;cursor:pointer}

  /* card-grid view */
  .card-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;margin-top:12px}
  .b-card{background:var(--card-bg);border-radius:12px;padding:14px;border:1px solid rgba(15,23,42,0.04);display:flex;gap:12px;align-items:center}
  .b-meta{display:flex;flex-direction:column;gap:6px}
  .b-name{font-weight:700;color:var(--text)}
  .b-sub{color:var(--muted);font-size:0.92rem}

  /* responsive */
  @media (max-width:880px){
    .controls{flex-direction:column;align-items:stretch}
    .controls-left{width:100%;justify-content:space-between}
    .search-input{width:100%}
  }
</style>

<div class="dashboard-wrap">
  <div class="panel">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:10px">
      <div>
        <h1>Boarders — <?=htmlspecialchars($hostel)?></h1>
        <div class="subtle">A clean, minimal view of boarder records. Manage details or switch to card view for a compact visual layout.</div>
      </div>
      <div style="display:flex;gap:10px;align-items:center">
        <a class="btn-subtle" href="add_boarder.php">+ Add boarder</a>
      </div>
    </div>

    <!-- Controls: search, filter, view toggle -->
    <div class="controls">
      <div class="controls-left">
        <form method="get" style="display:flex;gap:10px;align-items:center">
          <input class="search-input" name="q" value="<?=htmlspecialchars($q)?>" placeholder="Search name, roll, room, department or branch" />
          <select name="year" class="select-input">
            <option value="">All years</option>
            <!-- keep selection persistent -->
            <?php
              $years = ['1st','2nd','3rd','4th','Final','Other'];
              foreach($years as $y){
                $sel = ($filter_year === $y) ? 'selected' : '';
                echo "<option value=\"".htmlspecialchars($y)."\" $sel>$y</option>";
              }
            ?>
          </select>
          <button class="btn-subtle" type="submit">Filter</button>
          <a class="btn-subtle" href="dashboard.php" style="text-decoration:none">Reset</a>
        </form>
      </div>

      <div class="view-toggle">
        <div class="tiny muted" style="margin-right:6px">View</div>
        <button id="tableViewBtn" class="btn-subtle" onclick="toggleView('table')">Table</button>
        <button id="cardViewBtn" class="btn-subtle" onclick="toggleView('cards')">Cards</button>
      </div>
    </div>

    <!-- TABLE VIEW -->
    <div id="tableView">
      <table class="table-clean" aria-describedby="boarders">
        <thead>
          <tr>
            <th style="width:48px">#</th>
            <th style="width:72px">Photo</th>
            <th>Name</th>
            <th style="width:120px">Roll</th>
            <th style="width:100px">Room</th>
            <th>Department / Branch</th>
            <th style="width:90px">Year</th>
            <th style="width:120px">Contact</th>
            <th style="width:150px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!$boarders): ?>
            <tr><td colspan="9" class="tiny muted">No boarders found. Add your first boarder using "Add boarder".</td></tr>
          <?php else: foreach($boarders as $b): ?>
            <tr>
              <td class="tiny"><?=htmlspecialchars($b['id'])?></td>
              <td>
                <?php if($b['photo'] && file_exists($b['photo'])): ?>
                  <img class="avatar" src="<?=htmlspecialchars($b['photo'])?>" alt="photo">
                <?php else: ?>
                  <div style="width:56px;height:56px;border-radius:10px;background:#fbfbfb;border:1px solid rgba(15,23,42,0.03);display:flex;align-items:center;justify-content:center;color:var(--muted);font-weight:600">N/A</div>
                <?php endif; ?>
              </td>
              <td>
                <div style="font-weight:700;color:var(--text)"><?=htmlspecialchars($b['name'])?></div>
                <div class="tiny muted"><?=htmlspecialchars($b['department'])?> · <?=htmlspecialchars($b['branch'])?></div>
              </td>
              <td class="tiny"><?=htmlspecialchars($b['roll_number'])?></td>
              <td class="tiny"><?=htmlspecialchars($b['room_number'])?></td>
              <td class="tiny"><?=htmlspecialchars($b['department'])?> / <?=htmlspecialchars($b['branch'])?></td>
              <td class="tiny"><?=htmlspecialchars($b['year'])?></td>
              <td class="tiny"><?=htmlspecialchars($b['contact'])?></td>
              <td>
                <div class="actions">
                  <a class="action-edit" href="edit_boarder.php?id=<?=htmlspecialchars($b['id'])?>">Edit</a>
                  <a class="action-del" href="delete_boarder.php?id=<?=htmlspecialchars($b['id'])?>" onclick="return confirm('Delete this boarder?')">Delete</a>
                </div>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <!-- CARD GRID VIEW (hidden by default) -->
    <div id="cardView" style="display:none">
      <div class="card-grid">
        <?php if(!$boarders): ?>
          <div class="tiny muted">No boarders found.</div>
        <?php else: foreach($boarders as $b): ?>
          <div class="b-card">
            <div style="flex-shrink:0">
              <?php if($b['photo'] && file_exists($b['photo'])): ?>
                <img src="<?=htmlspecialchars($b['photo'])?>" class="avatar" alt="photo">
              <?php else: ?>
                <div style="width:56px;height:56px;border-radius:10px;background:#fbfbfb;border:1px solid rgba(15,23,42,0.03);display:flex;align-items:center;justify-content:center;color:var(--muted);font-weight:600">N/A</div>
              <?php endif; ?>
            </div>
            <div class="b-meta">
              <div class="b-name"><?=htmlspecialchars($b['name'])?></div>
              <div class="b-sub"><?=htmlspecialchars($b['roll_number'])?> · <?=htmlspecialchars($b['room_number'])?></div>
              <div class="tiny muted"><?=htmlspecialchars($b['department'])?> / <?=htmlspecialchars($b['branch'])?> · <?=htmlspecialchars($b['year'])?></div>
              <div style="margin-top:8px">
                <a class="action-edit" href="edit_boarder.php?id=<?=htmlspecialchars($b['id'])?>">Edit</a>
                <a class="action-del" href="delete_boarder.php?id=<?=htmlspecialchars($b['id'])?>" onclick="return confirm('Delete this boarder?')">Delete</a>
              </div>
            </div>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

  </div> <!-- panel -->
</div> <!-- dashboard-wrap -->

<script>
  // simple view toggle
  function toggleView(v){
    const table = document.getElementById('tableView');
    const cards = document.getElementById('cardView');
    const tableBtn = document.getElementById('tableViewBtn');
    const cardBtn = document.getElementById('cardViewBtn');

    if(v === 'cards'){
      table.style.display = 'none';
      cards.style.display = 'block';
      tableBtn.style.opacity = 0.6;
      cardBtn.style.opacity = 1;
    } else {
      table.style.display = 'block';
      cards.style.display = 'none';
      tableBtn.style.opacity = 1;
      cardBtn.style.opacity = 0.6;
    }
  }

  // set initial state (table visible)
  document.addEventListener('DOMContentLoaded', function(){
    toggleView('table');
  });
</script>

<?php require 'footer.php'; ?>
