<?php
session_start();
if (empty($_SESSION['emp_role'])) { header('Location: Index.php'); exit; }
include 'Connection.php';
$empId = $_SESSION['emp_id']; $brId = $_SESSION['br_id'];
$s = $pdo->prepare("SELECT COUNT(*) FROM invoice WHERE brId=?"); $s->execute([$brId]); $invoiceCount = $s->fetchColumn();
$s = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM invoice WHERE brId=?"); $s->execute([$brId]); $premiumTotal = $s->fetchColumn();
$branches  = $pdo->query("SELECT * FROM branch")->fetchAll();
$teamLeads = $pdo->query("SELECT emp_Id FROM employee WHERE emp_Role='TeamLead'")->fetchAll();
$supervisors = $pdo->query("SELECT emp_Id FROM employee WHERE emp_Role='Supervisor'")->fetchAll();
$agents    = $pdo->query("SELECT emp_Id FROM employee WHERE emp_Role='SalesAgent'")->fetchAll();
$s = $pdo->prepare("SELECT * FROM invoice WHERE brId=? ORDER BY created_at DESC LIMIT 5"); $s->execute([$brId]); $invoiceList = $s->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Cashier Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboard.css">
</head><body>
<nav class="topbar">
    <div class="topbar-brand">📊 <span>Sales</span>Performance</div>
    <div class="topbar-user"><span>Cashier</span><span class="user-badge"><?= htmlspecialchars($empId) ?></span></div>
</nav>
<aside class="sidebar"><?php include 'NavigationBar.php'; ?></aside>
<main class="main">
    <div class="page-header"><h1>Cashier Dashboard</h1><p>Process and track invoices</p></div>
    <div class="stats-grid">
        <div class="stat-card" style="--accent-color:#4f8aff;--icon-bg:rgba(79,138,255,0.12)"><div class="stat-icon">🧾</div><div class="stat-value"><?= $invoiceCount ?></div><div class="stat-label">Invoices Processed</div></div>
        <div class="stat-card" style="--accent-color:#00d4aa;--icon-bg:rgba(0,212,170,0.12)"><div class="stat-icon">💰</div><div class="stat-value">$<?= number_format($premiumTotal/1000,1) ?>k</div><div class="stat-label">Premium Collected</div></div>
    </div>
    <div class="grid-2">
        <div class="card">
            <div class="card-header"><h3>Submit New Invoice</h3></div>
            <div class="card-body">
                <div id="formResponse"></div>
                <form id="cashierForm">
                    <div class="form-group"><label>Branch</label><select class="form-control" name="branchCode" required>
                        <option value="">Select Branch</option>
                        <?php foreach($branches as $b):?><option value="<?= htmlspecialchars($b['br_Id']) ?>"><?= htmlspecialchars($b['br_Name']) ?></option><?php endforeach;?>
                    </select></div>
                    <div class="form-group"><label>Team Leader</label><select class="form-control" name="teamLeaderCode" required>
                        <option value="">Select Team Leader</option>
                        <?php foreach($teamLeads as $tl):?><option value="<?= htmlspecialchars($tl['emp_Id']) ?>"><?= htmlspecialchars($tl['emp_Id']) ?></option><?php endforeach;?>
                    </select></div>
                    <div class="form-group"><label>Supervisor</label><select class="form-control" name="supervisorCode" required>
                        <option value="">Select Supervisor</option>
                        <?php foreach($supervisors as $sup):?><option value="<?= htmlspecialchars($sup['emp_Id']) ?>"><?= htmlspecialchars($sup['emp_Id']) ?></option><?php endforeach;?>
                    </select></div>
                    <div class="form-group"><label>Sales Agent</label><select class="form-control" name="salesAgentCode" required>
                        <option value="">Select Sales Agent</option>
                        <?php foreach($agents as $ag):?><option value="<?= htmlspecialchars($ag['emp_Id']) ?>"><?= htmlspecialchars($ag['emp_Id']) ?></option><?php endforeach;?>
                    </select></div>
                    <div class="form-group"><label>Policy Number</label><input type="text" class="form-control" name="policyNumber" placeholder="e.g. POL-001" required></div>
                    <div class="form-group"><label>Invoice Date</label><input type="date" class="form-control" name="invoiceDate" required></div>
                    <div class="form-group"><label>Cash Handed Over Date</label><input type="date" class="form-control" name="cashHandOverDate" required></div>
                    <div class="form-group"><label>Premium Amount</label><input type="number" class="form-control" name="premiumAmount" placeholder="0.00" required></div>
                    <div class="form-group"><label>Payment Frequency</label><select class="form-control" name="paymentFrequent" required>
                        <option value="">Select Frequency</option>
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                        <option value="daily">Daily</option>
                    </select></div>
                    <div class="form-group"><label>Agent Signature</label><input type="text" class="form-control" name="agentSignature" placeholder="Agent name"></div>
                    <button type="submit" class="btn btn-primary btn-block">Submit Invoice</button>
                </form>
            </div>
        </div>
        <div class="card"><div class="card-header"><h3>Recent Invoices</h3></div><div class="card-body" style="padding:0"><div class="table-wrap"><table>
            <thead><tr><th>Policy</th><th>Amount</th><th>Date</th></tr></thead><tbody>
            <?php if(empty($invoiceList)):?><tr><td colspan="3"><div class="empty"><div class="empty-icon">🧾</div>No invoices yet</div></td></tr>
            <?php else: foreach($invoiceList as $inv):?>
            <tr><td><?= htmlspecialchars($inv['policyNumber']) ?></td>
            <td style="color:var(--success)">$<?= number_format($inv['amount'],2) ?></td>
            <td style="color:var(--text-muted)"><?= $inv['IssueDate'] ?></td></tr>
            <?php endforeach; endif; ?>
            </tbody></table></div></div></div>
    </div>
</main>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#cashierForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({ url:'DataCashier.php', type:'POST', data:$(this).serialize(),
        success: function(data) {
            if(data==='success') {
                $('#formResponse').html('<div class="alert alert-success">✅ Invoice submitted successfully!</div>');
                $('#cashierForm')[0].reset();
                setTimeout(()=>location.reload(), 1500);
            } else {
                $('#formResponse').html('<div class="alert alert-danger">❌ Error: '+data+'</div>');
            }
        },
        error: function() { $('#formResponse').html('<div class="alert alert-danger">❌ Server error.</div>'); }
    });
});
</script>
</body></html>