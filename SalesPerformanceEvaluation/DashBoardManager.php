<?php
session_start();
if (empty($_SESSION['emp_role'])) { header('Location: Index.php'); exit; }
include 'Connection.php';
$brId = $_SESSION['br_id'];
$totalSales    = $pdo->query("SELECT COUNT(*) FROM totalsales")->fetchColumn();
$totalAgents   = $pdo->query("SELECT COUNT(*) FROM employee WHERE emp_Role='SalesAgent'")->fetchColumn();
$totalLeads    = $pdo->query("SELECT COUNT(*) FROM employee WHERE emp_Role='TeamLead'")->fetchColumn();
$totalPremium  = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM invoice")->fetchColumn();
$s = $pdo->prepare("SELECT COUNT(*) FROM target WHERE br_Id=?"); $s->execute([$brId]); $targetsCount = $s->fetchColumn();
$s = $pdo->prepare("SELECT COUNT(*) FROM invoice WHERE brId=?"); $s->execute([$brId]); $invoiceCount = $s->fetchColumn();
$s = $pdo->prepare("SELECT * FROM target WHERE br_Id=? ORDER BY start_Time DESC LIMIT 5"); $s->execute([$brId]); $targetList = $s->fetchAll();
$salesList = $pdo->query("SELECT t.sale_id, t.sale_Name, t.emp_Id, e.emp_Role FROM totalsales t LEFT JOIN employee e ON t.emp_Id=e.emp_Id ORDER BY t.sale_id DESC LIMIT 8")->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Manager Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboard.css">
</head><body>
<nav class="topbar">
    <div class="topbar-brand">📊 <span>Sales</span>Performance</div>
    <div class="topbar-user"><span>Manager</span><span class="user-badge"><?= htmlspecialchars($_SESSION['emp_id']) ?></span></div>
</nav>
<aside class="sidebar"><?php include 'NavigationBar.php'; ?></aside>
<main class="main">
    <div class="page-header"><h1>Manager Dashboard</h1><p>Branch performance overview</p></div>
    <div class="stats-grid">
        <div class="stat-card" style="--accent-color:#4f8aff;--icon-bg:rgba(79,138,255,0.12)"><div class="stat-icon">📈</div><div class="stat-value"><?= $totalSales ?></div><div class="stat-label">Total Sales</div></div>
        <div class="stat-card" style="--accent-color:#00d4aa;--icon-bg:rgba(0,212,170,0.12)"><div class="stat-icon">🧑‍💼</div><div class="stat-value"><?= $totalAgents ?></div><div class="stat-label">Sales Agents</div></div>
        <div class="stat-card" style="--accent-color:#7c5cfc;--icon-bg:rgba(124,92,252,0.12)"><div class="stat-icon">👑</div><div class="stat-value"><?= $totalLeads ?></div><div class="stat-label">Team Leads</div></div>
        <div class="stat-card" style="--accent-color:#ffb347;--icon-bg:rgba(255,179,71,0.12)"><div class="stat-icon">🎯</div><div class="stat-value"><?= $targetsCount ?></div><div class="stat-label">Targets</div></div>
        <div class="stat-card" style="--accent-color:#ff4d6d;--icon-bg:rgba(255,77,109,0.12)"><div class="stat-icon">🧾</div><div class="stat-value"><?= $invoiceCount ?></div><div class="stat-label">Invoices</div></div>
        <div class="stat-card" style="--accent-color:#00d4aa;--icon-bg:rgba(0,212,170,0.12)"><div class="stat-icon">💰</div><div class="stat-value">$<?= number_format($totalPremium/1000,1) ?>k</div><div class="stat-label">Premium</div></div>
    </div>
    <div class="grid-2">
        <div class="card"><div class="card-header"><h3>Branch Targets</h3></div><div class="card-body" style="padding:0"><div class="table-wrap"><table>
            <thead><tr><th>ID</th><th>Amount</th><th>Status</th><th>Deadline</th></tr></thead><tbody>
            <?php if(empty($targetList)):?><tr><td colspan="4"><div class="empty"><div class="empty-icon">🎯</div>No targets set</div></td></tr>
            <?php else: foreach($targetList as $t):?>
            <tr><td style="font-family:'Syne',sans-serif;font-weight:700"><?= htmlspecialchars($t['tr_Id']) ?></td>
            <td style="color:var(--success)">$<?= number_format($t['amount']) ?></td>
            <td><span class="badge badge-<?= $t['status']==='done'?'success':'warning' ?>"><?= htmlspecialchars($t['status']) ?></span></td>
            <td style="color:var(--text-muted)"><?= $t['end_Time'] ?></td></tr>
            <?php endforeach; endif; ?>
            </tbody></table></div></div></div>
        <div class="card"><div class="card-header"><h3>Recent Sales</h3></div><div class="card-body" style="padding:0"><div class="table-wrap"><table>
            <thead><tr><th>Sale ID</th><th>Product</th><th>Agent</th></tr></thead><tbody>
            <?php if(empty($salesList)):?><tr><td colspan="3"><div class="empty"><div class="empty-icon">📊</div>No sales yet</div></td></tr>
            <?php else: foreach($salesList as $s):?>
            <tr><td style="font-family:'Syne',sans-serif;font-weight:700"><?= htmlspecialchars($s['sale_id']) ?></td>
            <td><?= htmlspecialchars($s['sale_Name']) ?></td>
            <td style="color:var(--text-muted)"><?= htmlspecialchars($s['emp_Id']) ?></td></tr>
            <?php endforeach; endif; ?>
            </tbody></table></div></div></div>
    </div>
</main>
</body></html>