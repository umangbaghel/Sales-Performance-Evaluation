<?php
session_start();
if (empty($_SESSION['emp_role'])) { header('Location: Index.php'); exit; }
include 'Connection.php';

$totalEmployees = $pdo->query("SELECT COUNT(*) FROM employee")->fetchColumn();
$totalBranches  = $pdo->query("SELECT COUNT(*) FROM branch")->fetchColumn();
$totalSales     = $pdo->query("SELECT COUNT(*) FROM totalsales")->fetchColumn();
$totalTargets   = $pdo->query("SELECT COUNT(*) FROM target")->fetchColumn();
$totalInvoices  = $pdo->query("SELECT COUNT(*) FROM invoice")->fetchColumn();
$totalPremium   = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM invoice")->fetchColumn();
$roleData       = $pdo->query("SELECT emp_Role, COUNT(*) as count FROM employee GROUP BY emp_Role")->fetchAll();
$recentInvoices = $pdo->query("SELECT * FROM invoice ORDER BY created_at DESC LIMIT 5")->fetchAll();
$employees      = $pdo->query("SELECT e.emp_Id, e.emp_Role, b.br_Name FROM employee e LEFT JOIN branch b ON e.br_Id = b.br_Id")->fetchAll();
$roleLabels = array_column($roleData, 'emp_Role');
$roleCounts = array_column($roleData, 'count');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboard.css">
</head>
<body>
<nav class="topbar">
    <div class="topbar-brand">📊 <span>Sales</span>Performance</div>
    <div class="topbar-user">
        <span>Admin</span>
        <span class="user-badge"><?= htmlspecialchars($_SESSION['emp_id']) ?></span>
    </div>
</nav>
<aside class="sidebar">
    <?php include 'NavigationBar.php'; ?>
</aside>
<main class="main">
    <div class="page-header">
        <h1>Admin Dashboard</h1>
        <p>Overview of all branch performance metrics</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card" style="--accent-color:#4f8aff;--icon-bg:rgba(79,138,255,0.12)">
            <div class="stat-icon">👥</div>
            <div class="stat-value"><?= $totalEmployees ?></div>
            <div class="stat-label">Employees</div>
        </div>
        <div class="stat-card" style="--accent-color:#00d4aa;--icon-bg:rgba(0,212,170,0.12)">
            <div class="stat-icon">🏢</div>
            <div class="stat-value"><?= $totalBranches ?></div>
            <div class="stat-label">Branches</div>
        </div>
        <div class="stat-card" style="--accent-color:#7c5cfc;--icon-bg:rgba(124,92,252,0.12)">
            <div class="stat-icon">📈</div>
            <div class="stat-value"><?= $totalSales ?></div>
            <div class="stat-label">Total Sales</div>
        </div>
        <div class="stat-card" style="--accent-color:#ffb347;--icon-bg:rgba(255,179,71,0.12)">
            <div class="stat-icon">🎯</div>
            <div class="stat-value"><?= $totalTargets ?></div>
            <div class="stat-label">Targets</div>
        </div>
        <div class="stat-card" style="--accent-color:#ff4d6d;--icon-bg:rgba(255,77,109,0.12)">
            <div class="stat-icon">🧾</div>
            <div class="stat-value"><?= $totalInvoices ?></div>
            <div class="stat-label">Invoices</div>
        </div>
        <div class="stat-card" style="--accent-color:#00d4aa;--icon-bg:rgba(0,212,170,0.12)">
            <div class="stat-icon">💰</div>
            <div class="stat-value">$<?= number_format($totalPremium/1000,1) ?>k</div>
            <div class="stat-label">Premium</div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-header"><h3>Employees by Role</h3></div>
            <div class="card-body">
                <div class="chart-wrap"><canvas id="roleChart"></canvas></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Recent Invoices</h3></div>
            <div class="card-body" style="padding:0">
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Invoice</th><th>Policy</th><th>Amount</th><th>Date</th></tr></thead>
                        <tbody>
                        <?php if(empty($recentInvoices)):?>
                        <tr><td colspan="4"><div class="empty"><div class="empty-icon">📭</div>No invoices yet</div></td></tr>
                        <?php else: foreach($recentInvoices as $inv):?>
                        <tr>
                            <td><?= htmlspecialchars($inv['invoice_Id']) ?></td>
                            <td><?= htmlspecialchars($inv['policyNumber']) ?></td>
                            <td style="color:var(--success)">$<?= number_format($inv['amount'],2) ?></td>
                            <td style="color:var(--text-muted)"><?= $inv['IssueDate'] ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>All Employees</h3></div>
        <div class="card-body" style="padding:0">
            <div class="table-wrap">
                <table>
                    <thead><tr><th>ID</th><th>Role</th><th>Branch</th></tr></thead>
                    <tbody>
                    <?php foreach($employees as $emp):?>
                    <tr>
                        <td style="font-family:'Syne',sans-serif;font-weight:700"><?= htmlspecialchars($emp['emp_Id']) ?></td>
                        <td><span class="badge badge-info"><?= htmlspecialchars($emp['emp_Role']) ?></span></td>
                        <td style="color:var(--text-muted)"><?= htmlspecialchars($emp['br_Name']??'') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.color = '#6b7a99';
Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
new Chart(document.getElementById('roleChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($roleLabels) ?>,
        datasets: [{
            data: <?= json_encode($roleCounts) ?>,
            backgroundColor: ['#4f8aff','#00d4aa','#ff4d6d','#ffb347','#7c5cfc','#6b7a99'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyleWidth: 8 } }
        }
    }
});
</script>
</body>
</html>