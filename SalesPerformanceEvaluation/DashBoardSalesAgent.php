<?php
session_start();
if (empty($_SESSION['emp_role'])) { header('Location: Index.php'); exit; }
include 'Connection.php';

$empId = $_SESSION['emp_id'];
$brId  = $_SESSION['br_id'];

$mySales    = $pdo->prepare("SELECT COUNT(*) FROM totalsales WHERE emp_Id=?");
$mySales->execute([$empId]); $mySalesCount = $mySales->fetchColumn();

$myInvoices = $pdo->prepare("SELECT COUNT(*) FROM invoice WHERE saleId=?");
$myInvoices->execute([$empId]); $myInvoiceCount = $myInvoices->fetchColumn();

$myPremium  = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM invoice WHERE saleId=?");
$myPremium->execute([$empId]); $myPremiumTotal = $myPremium->fetchColumn();

$targets = $pdo->prepare("SELECT * FROM target WHERE br_Id=? ORDER BY start_Time DESC LIMIT 3");
$targets->execute([$brId]); $targetList = $targets->fetchAll();

$salesStmt = $pdo->prepare("SELECT * FROM totalsales WHERE emp_Id=? ORDER BY sale_id DESC LIMIT 10");
$salesStmt->execute([$empId]); $mySalesList = $salesStmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales Agent Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>body{background:#f4f6f9}.sidebar{min-height:100vh;background:#2c3e50;padding-top:20px}.sidebar .nav-link{color:#bdc3c7;padding:10px 20px}.sidebar .nav-link:hover{color:#fff}.navbar{background:#2c3e50!important}</style>
</head>
<body>
<nav class="navbar navbar-dark"><span class="navbar-brand font-weight-bold">📊 Sales Performance System</span>
<span class="text-white small">Agent: <?=htmlspecialchars($empId)?></span></nav>
<div class="container-fluid"><div class="row">
<nav class="col-md-2 sidebar d-none d-md-block"><?php include 'NavigationBar.php';?></nav>
<main class="col-md-10 p-4">
    <h4 class="mb-4 font-weight-bold">Sales Agent Dashboard</h4>
    <div class="row">
        <div class="col-md-4"><div class="card text-white bg-primary mb-3 p-3 text-center"><h3><?=$mySalesCount?></h3><small>My Sales</small></div></div>
        <div class="col-md-4"><div class="card text-white bg-success mb-3 p-3 text-center"><h3><?=$myInvoiceCount?></h3><small>My Invoices</small></div></div>
        <div class="col-md-4"><div class="card text-white bg-danger mb-3 p-3 text-center"><h3>$<?=number_format($myPremiumTotal,2)?></h3><small>Total Premium Collected</small></div></div>
    </div>
    <div class="row mt-2">
        <div class="col-md-6"><div class="card p-3"><h6 class="font-weight-bold">My Sales</h6>
            <table class="table table-sm table-hover"><thead class="thead-light"><tr><th>Sale ID</th><th>Name</th></tr></thead><tbody>
            <?php if(empty($mySalesList)):?><tr><td colspan="2" class="text-center text-muted">No sales yet</td></tr>
            <?php else: foreach($mySalesList as $s):?>
            <tr><td><?=htmlspecialchars($s['sale_id'])?></td><td><?=htmlspecialchars($s['sale_Name'])?></td></tr>
            <?php endforeach;endif;?></tbody></table>
        </div></div>
        <div class="col-md-6"><div class="card p-3"><h6 class="font-weight-bold">Branch Targets</h6>
            <table class="table table-sm"><thead class="thead-light"><tr><th>Amount</th><th>Status</th><th>Deadline</th></tr></thead><tbody>
            <?php if(empty($targetList)):?><tr><td colspan="3" class="text-center text-muted">No targets</td></tr>
            <?php else: foreach($targetList as $t):?>
            <tr><td><?=htmlspecialchars($t['amount'])?></td>
            <td><span class="badge badge-<?=$t['status']==='done'?'success':'warning'?>"><?=htmlspecialchars($t['status'])?></span></td>
            <td><?=$t['end_Time']?></td></tr>
            <?php endforeach;endif;?></tbody></table>
        </div></div>
    </div>
</main></div></div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body></html>