<?php
session_start();
if (empty($_SESSION['emp_role'])) { header('Location: Index.php'); exit; }
include 'Connection.php';
$empId = $_SESSION['emp_id']; $brId = $_SESSION['br_id'];
$s = $pdo->prepare("SELECT COUNT(*) FROM totalsales WHERE emp_Id=?"); $s->execute([$empId]); $mySales = $s->fetchColumn();
$s = $pdo->prepare("SELECT COUNT(*) FROM invoice WHERE saleId=?"); $s->execute([$empId]); $myInvoices = $s->fetchColumn();
$s = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM invoice WHERE saleId=?"); $s->execute([$empId]); $myPremium = $s->fetchColumn();
$s = $pdo->prepare("SELECT * FROM target WHERE br_Id=? ORDER BY end_Time ASC LIMIT 3"); $s->execute([$brId]); $targets = $s->fetchAll();
$s = $pdo->prepare("SELECT * FROM totalsales WHERE emp_Id=? ORDER BY sale_id DESC LIMIT 10"); $s->execute([$empId]); $mySalesList = $s->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Sales Agent Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboard.css">
</head><body>
<nav class="topbar">
    <div class="topbar-brand">📊 <span>Sales</span>Performance</div>
    <div class="topbar-user"><span>Sales Agent</span><span class="user-badge"><?= htmlspecialchars($empId) ?></span></div>
</nav>
<aside class="sidebar"><?php include 'NavigationBar.php'; ?></aside>
<main class="main">
    <div class="page-header"><h1>My Performance</h1><p>Track your sales and targets</p></div>
    <div class="stats-grid">
        <div class="stat-card" style="--accent-color:#4f8aff;--icon-bg:rgba(79,138,255,0.12)"><div class="stat-icon">📈</div><div class="stat-value"><?= $mySales ?></div><div class="stat-label">My Sales</div></div>
        <div class="stat-card" style="--accent-color:#7c5cfc;--icon-bg:rgba(124,92,252,0.12)"><div class="stat-icon">🧾</div><div class="stat-value"><?= $myInvoices ?></div><div class="stat-label">My Invoices</div></div>
        <div class="stat-card" style="--accent-color:#00d4aa;--icon-bg:rgba(0,212,170,0.12)"><div class="stat-icon">💰</div><div class="stat-value">$<?= number_format($myPremium/1000,1) ?>k</div><div class="stat-label">Premium Collected</div></div>
    </div>
    <div class="grid-2">
        <div class="card"><div class="card-header"><h3>My Sales</h3></div><div class="card-body" style="padding:0"><div class="table-wrap"><table>
            <thead><tr><th>Sale ID</th><th>Product</th></tr></thead><tbody>
            <?php if(empty($mySalesList)):?><tr><td colspan="2"><div class="empty"><div class="empty-icon">📊</div>No sales yet</div></td></tr>
            <?php else: foreach($mySalesList as $s):?>
            <tr><td style="font-family:'Syne',sans-serif;font-weight:700"><?= htmlspecialchars($s['sale_id']) ?></td><td><?= htmlspecialchars($s['sale_Name']) ?></td></tr>
            <?php endforeach; endif; ?>
            </tbody></table></div></div></div>
        <div class="card"><div class="card-header"><h3>Branch Targets</h3></div><div class="card-body" style="padding:0"><div class="table-wrap"><table>
            <thead><tr><th>Amount</th><th>Status</th><th>Deadline</th></tr></thead><tbody>
            <?php if(empty($targets)):?><tr><td colspan="3"><div class="empty"><div class="empty-icon">🎯</div>No targets</div></td></tr>
            <?php else: foreach($targets as $t):?>
            <tr><td style="color:var(--success)">$<?= number_format($t['amount']) ?></td>
            <td><span class="badge badge-<?= $t['status']==='done'?'success':'warning' ?>"><?= htmlspecialchars($t['status']) ?></span></td>
            <td style="color:var(--text-muted)"><?= $t['end_Time'] ?></td></tr>
            <?php endforeach; endif; ?>
            </tbody></table></div></div></div>
    </div>
</main>
</body></html>