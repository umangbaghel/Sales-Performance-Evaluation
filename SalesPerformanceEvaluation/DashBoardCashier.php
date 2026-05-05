<?php
session_start();
if (empty($_SESSION['emp_role'])) { header('Location: Index.php'); exit; }
include 'Connection.php';

$empId = $_SESSION['emp_id'];
$brId  = $_SESSION['br_id'];

$myInvoices  = $pdo->prepare("SELECT COUNT(*) FROM invoice WHERE brId=?");
$myInvoices->execute([$brId]); $invoiceCount = $myInvoices->fetchColumn();

$myPremium   = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM invoice WHERE brId=?");
$myPremium->execute([$brId]); $premiumTotal = $myPremium->fetchColumn();

// Load branches and employees for dropdowns (dynamic!)
$branches  = $pdo->query("SELECT * FROM branch")->fetchAll();
$teamLeads = $pdo->query("SELECT emp_Id FROM employee WHERE emp_Role='TeamLead'")->fetchAll();
$supervisors = $pdo->query("SELECT emp_Id FROM employee WHERE emp_Role='Supervisor'")->fetchAll();
$agents    = $pdo->query("SELECT emp_Id FROM employee WHERE emp_Role='SalesAgent'")->fetchAll();

$recentInvoices = $pdo->prepare("SELECT * FROM invoice WHERE brId=? ORDER BY created_at DESC LIMIT 5");
$recentInvoices->execute([$brId]); $invoiceList = $recentInvoices->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cashier Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>body{background:#f4f6f9}.sidebar{min-height:100vh;background:#2c3e50;padding-top:20px}.sidebar .nav-link{color:#bdc3c7;padding:10px 20px}.sidebar .nav-link:hover{color:#fff}.navbar{background:#2c3e50!important}</style>
</head>
<body>
<nav class="navbar navbar-dark"><span class="navbar-brand font-weight-bold">📊 Sales Performance System</span>
<span class="text-white small">Cashier: <?=htmlspecialchars($empId)?></span></nav>
<div class="container-fluid"><div class="row">
<nav class="col-md-2 sidebar d-none d-md-block"><?php include 'NavigationBar.php';?></nav>
<main class="col-md-10 p-4">
    <h4 class="mb-4 font-weight-bold">Cashier Dashboard</h4>
    <div class="row">
        <div class="col-md-6"><div class="card text-white bg-primary mb-3 p-3 text-center"><h3><?=$invoiceCount?></h3><small>Invoices Processed</small></div></div>
        <div class="col-md-6"><div class="card text-white bg-success mb-3 p-3 text-center"><h3>$<?=number_format($premiumTotal,2)?></h3><small>Total Premium Collected</small></div></div>
    </div>

    <div class="row mt-2">
        <div class="col-md-7">
            <div class="card p-4">
                <h6 class="font-weight-bold mb-3">Submit New Invoice</h6>
                <div id="formResponse"></div>
                <form id="cashierForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Branch</label>
                                <select class="form-control" name="branchCode" required>
                                    <option value="">Select Branch</option>
                                    <?php foreach($branches as $b):?>
                                    <option value="<?=htmlspecialchars($b['br_Id'])?>"><?=htmlspecialchars($b['br_Name'])?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Team Leader Code</label>
                                <select class="form-control" name="teamLeaderCode" required>
                                    <option value="">Select Team Leader</option>
                                    <?php foreach($teamLeads as $tl):?>
                                    <option value="<?=htmlspecialchars($tl['emp_Id'])?>"><?=htmlspecialchars($tl['emp_Id'])?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Invoice Date</label>
                                <input type="date" class="form-control" name="invoiceDate" required>
                            </div>
                            <div class="form-group">
                                <label>Premium Amount</label>
                                <input type="number" class="form-control" name="premiumAmount" placeholder="Enter amount" required>
                            </div>
                            <div class="form-group">
                                <label>Agent Signature</label>
                                <input type="text" class="form-control" name="agentSignature" placeholder="Agent Signature">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Supervisor Code</label>
                                <select class="form-control" name="supervisorCode" required>
                                    <option value="">Select Supervisor</option>
                                    <?php foreach($supervisors as $sup):?>
                                    <option value="<?=htmlspecialchars($sup['emp_Id'])?>"><?=htmlspecialchars($sup['emp_Id'])?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Sales Agent Code</label>
                                <select class="form-control" name="salesAgentCode" required>
                                    <option value="">Select Sales Agent</option>
                                    <?php foreach($agents as $ag):?>
                                    <option value="<?=htmlspecialchars($ag['emp_Id'])?>"><?=htmlspecialchars($ag['emp_Id'])?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Policy Number</label>
                                <input type="text" class="form-control" name="policyNumber" placeholder="Enter Policy Number" required>
                            </div>
                            <div class="form-group">
                                <label>Payment Frequency</label>
                                <select class="form-control" name="paymentFrequent" required>
                                    <option value="">Select Frequency</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="daily">Daily</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Cash Handed Over Date</label>
                                <input type="date" class="form-control" name="cashHandOverDate" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Submit Invoice</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-5"><div class="card p-3"><h6 class="font-weight-bold">Recent Invoices</h6>
            <table class="table table-sm table-hover"><thead class="thead-light"><tr><th>Policy</th><th>Amount</th><th>Date</th></tr></thead><tbody>
            <?php if(empty($invoiceList)):?><tr><td colspan="3" class="text-center text-muted">No invoices yet</td></tr>
            <?php else: foreach($invoiceList as $inv):?>
            <tr><td><?=htmlspecialchars($inv['policyNumber'])?></td><td>$<?=number_format($inv['amount'],2)?></td><td><?=$inv['IssueDate']?></td></tr>
            <?php endforeach;endif;?></tbody></table>
        </div></div>
    </div>
</main></div></div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
    $('#cashierForm').on('submit',function(e){
        e.preventDefault();
        $.ajax({url:'DataCashier.php',type:'POST',data:$(this).serialize(),
        success:function(data){
            if(data==='success'){
                $('#formResponse').html('<div class="alert alert-success">Invoice submitted successfully!</div>');
                $('#cashierForm')[0].reset();
                setTimeout(()=>location.reload(),1500);
            } else {
                $('#formResponse').html('<div class="alert alert-danger">Error: '+data+'</div>');
            }
        },error:function(){$('#formResponse').html('<div class="alert alert-danger">Server error.</div>');}
        });
    });
});
</script>
</body></html>