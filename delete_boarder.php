<?php
// delete_boarder.php
require 'config.php';
ensure_logged_in();

$hostel = $_SESSION['warden_hostel'];
$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT photo FROM boarders WHERE id = ? AND hostel = ?");
$stmt->execute([$id, $hostel]);
$row = $stmt->fetch();
if ($row) {
    $pdo->prepare("DELETE FROM boarders WHERE id = ? AND hostel = ?")->execute([$id, $hostel]);
    if ($row['photo'] && file_exists($row['photo'])) unlink($row['photo']);
    header('Location: dashboard.php?deleted=1');
    exit;
} else {
    // not found or access denied
    header('Location: dashboard.php');
    exit;
}
