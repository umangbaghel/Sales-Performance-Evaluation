<?php
session_start();
if (empty($_SESSION['emp_role'])) { header('Location: Index.php'); exit; }
include 'Connection.php';

$brId = $_SESSION['br_id'];

$totalSales    = $pdo->query("SELECT COUNT(*) FROM totalsales")->fetchColumn();
$totalTargets  = $pdo->query("SELECT COUNT(*) FROM target WHERE br_Id='$brId'")->fetchColumn();
$totalInvoices = $pdo->query("SELECT COUNT(*) FROM invoice WHERE brId='$brId'")->fetchColumn();
$totalPremium  = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM invoice WHERE brId='$brId'")->fetchColumn();
$totalAgents   = $pdo->query("SELECT COUNT(*) FROM employee WHERE emp_Role='SalesAgent'")->fetchColumn();
$totalLeads    = $pdo->query("SELECT COUNT(*) FROM employee WHERE emp_Role='TeamLead'")->fetchColumn();

$targets = $pdo->prepare("SELECT * FROM target WHERE br_Id=? ORDER BY start_Time DESC LIMIT 5");
$targets->execute([$brId]);
$targetList = $targets->fetchAll();

$sales = $pdo->query("SELECT t.sale_id, t.sale_Name, t.emp_Id, e.emp_Role FROM totalsales t LEFT JOIN employee e ON t.emp_Id=e.emp_Id ORDER BY t.sale_id DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>body{background:#f4f6f9}.sidebar{min-height:100vh;background:#2c3e50;padding-top:20px}.sidebar .nav-link{color:#bdc3c7;padding:10px 20px}.sidebar .nav-link:hover{color:#fff}.navbar{background:#2c3e50!important}</style>
</head>
<body>
<nav class="navbar navbar-dark"><span class="navbar-brand font-weight-bold">📊 Sales Performance System</span>
<span class="text-white small">Manager: <?=htmlspecialchars($_SESSION['emp_id'])?></span></nav>
<div class="container-fluid"><div class="row">
<nav class="col-md-2 sidebar d-none d-md-block"><?php include 'NavigationBar.php';?></nav>
<main class="col-md-10 p-4">
    <h4 class="mb-4 font-weight-bold">Manager Dashboard</h4>
    <div class="row">
        <div class="col-md-2"><div class="card text-white bg-primary mb-3 p-3 text-center"><h3><?=$totalSales?></h3><small>Total Sales</small></div></div>
        <div class="col-md-2"><div class="card text-white bg-success mb-3 p-3 text-center"><h3><?=$totalAgents?></h3><small>Sales Agents</small></div></div>
        <div class="col-md-2"><div class="card text-white bg-info mb-3 p-3 text-center"><h3><?=$totalLeads?></h3><small>Team Leads</small></div></div>
        <div class="col-md-2"><div class="card text-white bg-warning mb-3 p-3 text-center"><h3><?=$totalTargets?></h3><small>Targets</small></div></div>
        <div class="col-md-2"><div class="card text-white bg-danger mb-3 p-3 text-center"><h3><?=$totalInvoices?></h3><small>Invoices</small></div></div>
        <div class="col-md-2"><div class="card text-white bg-dark mb-3 p-3 text-center"><h3>$<?=number_format($totalPremium,0)?></h3><small>Premium</small></div></div>
    </div>
    <div class="row mt-2">
        <div class="col-md-6"><div class="card p-3"><h6 class="font-weight-bold">Branch Targets</h6>
            <table class="table table-sm table-hover"><thead class="thead-light"><tr><th>ID</th><th>Amount</th><th>Status</th><th>End Date</th></tr></thead><tbody>
            <?php if(empty($targetList)):?><tr><td colspan="4" class="text-center text-muted">No targets set</td></tr>
            <?php else: foreach($targetList as $t):?>
            <tr><td><?=htmlspecialchars($t['tr_Id'])?></td><td><?=htmlspecialchars($t['amount'])?></td>
            <td><span class="badge badge-<?=$t['status']==='done'?'success':'warning'?>"><?=htmlspecialchars($t['status'])?></span></td>
            <td><?=$t['end_Time']?></td></tr>
            <?php endforeach;endif;?></tbody></table>
        </div></div>
        <div class="col-md-6"><div class="card p-3"><h6 class="font-weight-bold">Recent Sales</h6>
            <table class="table table-sm table-hover"><thead class="thead-light"><tr><th>Sale ID</th><th>Name</th><th>Agent</th><th>Role</th></tr></thead><tbody>
            <?php if(empty($sales)):?><tr><td colspan="4" class="text-center text-muted">No sales yet</td></tr>
            <?php else: foreach($sales as $s):?>
            <tr><td><?=htmlspecialchars($s['sale_id'])?></td><td><?=htmlspecialchars($s['sale_Name'])?></td>
            <td><?=htmlspecialchars($s['emp_Id'])?></td><td><?=htmlspecialchars($s['emp_Role']??'')?></td></tr>
            <?php endforeach;endif;?></tbody></table>
        </div></div>
    </div>
</main></div></div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body></html>