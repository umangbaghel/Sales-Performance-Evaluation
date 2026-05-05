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

$roleStmt = $pdo->query("SELECT emp_Role, COUNT(*) as count FROM employee GROUP BY emp_Role");
$roleData = $roleStmt->fetchAll();
$roleLabels = array_column($roleData, 'emp_Role');
$roleCounts = array_column($roleData, 'count');

$recentInvoices = $pdo->query("SELECT * FROM invoice ORDER BY created_at DESC LIMIT 5")->fetchAll();
$employees = $pdo->query("SELECT e.emp_Id, e.emp_Role, b.br_Name FROM employee e LEFT JOIN branch b ON e.br_Id = b.br_Id")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body{background:#f4f6f9}.sidebar{min-height:100vh;background:#2c3e50;padding-top:20px}
        .sidebar .nav-link{color:#bdc3c7;padding:10px 20px}.sidebar .nav-link:hover{color:#fff;background:rgba(255,255,255,0.1);border-radius:5px}
        .stat-card{border:none;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.08)}
        .navbar{background:#2c3e50!important}
    </style>
</head>
<body>
<nav class="navbar navbar-dark"><span class="navbar-brand font-weight-bold">📊 Sales Performance System</span>
<span class="text-white small">Admin: <?= htmlspecialchars($_SESSION['emp_id']) ?></span></nav>
<div class="container-fluid">
<div class="row">
<nav class="col-md-2 sidebar d-none d-md-block"><?php include 'NavigationBar.php'; ?></nav>
<main class="col-md-10 p-4">
    <h4 class="mb-4 font-weight-bold">Admin Dashboard</h4>
    <div class="row">
        <div class="col-md-2"><div class="card stat-card text-white bg-primary mb-3 p-3 text-center"><h3><?= $totalEmployees ?></h3><small>Employees</small></div></div>
        <div class="col-md-2"><div class="card stat-card text-white bg-success mb-3 p-3 text-center"><h3><?= $totalBranches ?></h3><small>Branches</small></div></div>
        <div class="col-md-2"><div class="card stat-card text-white bg-info mb-3 p-3 text-center"><h3><?= $totalSales ?></h3><small>Sales</small></div></div>
        <div class="col-md-2"><div class="card stat-card text-white bg-warning mb-3 p-3 text-center"><h3><?= $totalTargets ?></h3><small>Targets</small></div></div>
        <div class="col-md-2"><div class="card stat-card text-white bg-danger mb-3 p-3 text-center"><h3><?= $totalInvoices ?></h3><small>Invoices</small></div></div>
        <div class="col-md-2"><div class="card stat-card text-white bg-dark mb-3 p-3 text-center"><h3>$<?= number_format($totalPremium,0) ?></h3><small>Premium</small></div></div>
    </div>
    <div class="row mt-2">
        <div class="col-md-5"><div class="card p-3"><h6 class="font-weight-bold">Employees by Role</h6><canvas id="roleChart" height="220"></canvas></div></div>
        <div class="col-md-7"><div class="card p-3"><h6 class="font-weight-bold">Recent Invoices</h6>
            <table class="table table-sm table-hover"><thead class="thead-light"><tr><th>Invoice ID</th><th>Policy</th><th>Amount</th><th>Date</th></tr></thead><tbody>
            <?php if(empty($recentInvoices)):?><tr><td colspan="4" class="text-center text-muted">No invoices yet</td></tr>
            <?php else: foreach($recentInvoices as $inv):?>
            <tr><td><?=htmlspecialchars($inv['invoice_Id'])?></td><td><?=htmlspecialchars($inv['policyNumber'])?></td>
            <td>$<?=number_format($inv['amount'],2)?></td><td><?=$inv['IssueDate']?></td></tr>
            <?php endforeach;endif;?></tbody></table>
        </div></div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12"><div class="card p-3"><h6 class="font-weight-bold">All Employees</h6>
            <table class="table table-hover"><thead class="thead-dark"><tr><th>ID</th><th>Role</th><th>Branch</th></tr></thead><tbody>
            <?php foreach($employees as $emp):?>
            <tr><td><?=htmlspecialchars($emp['emp_Id'])?></td><td><?=htmlspecialchars($emp['emp_Role'])?></td><td><?=htmlspecialchars($emp['br_Name']??'')?></td></tr>
            <?php endforeach;?></tbody></table>
        </div></div>
    </div>
</main></div></div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('roleChart'),{type:'doughnut',data:{labels:<?=json_encode($roleLabels)?>,
datasets:[{data:<?=json_encode($roleCounts)?>,backgroundColor:['#007bff','#28a745','#dc3545','#ffc107','#17a2b8','#6c757d']}]},
options:{responsive:true,plugins:{legend:{position:'bottom'}}}});
</script>
</body></html>