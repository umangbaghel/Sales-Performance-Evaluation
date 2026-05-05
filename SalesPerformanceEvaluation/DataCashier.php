<?php
// DataCashier.php - Handles cashier form submission
// Fixed: include was 'connection.php' (wrong case) → 'Connection.php'
// Fixed: $conn used but variable is $pdo in Connection.php
// Fixed: no invoice_Id was being generated → added uniqid()
// Fixed: no session check — added auth guard

session_start();
include_once 'Connection.php';

header('Content-Type: application/json');

// Auth guard: only cashiers can submit
if (empty($_SESSION['emp_role']) || strtolower($_SESSION['emp_role']) !== 'cashier') {
    http_response_code(403);
    echo json_encode('unauthorized');
    exit;
}

$branchCode       = $_POST['branchCode']      ?? '';
$invoiceDate      = $_POST['invoiceDate']      ?? '';
$teamLeaderCode   = $_POST['teamLeaderCode']   ?? '';
$premiumAmount    = $_POST['premiumAmount']    ?? 0;
$agentSignature   = $_POST['agentSignature']   ?? '';
$supervisorCode   = $_POST['supervisorCode']   ?? '';
$salesAgentCode   = $_POST['salesAgentCode']   ?? '';
$policyNumber     = $_POST['policyNumber']     ?? '';
$paymentFrequent  = $_POST['paymentFrequent']  ?? '';
$cashHandedOverDate = $_POST['cashHandOverDate'] ?? '';

// Basic validation
if (!$branchCode || !$invoiceDate || !$policyNumber) {
    echo json_encode('missing fields');
    exit;
}

// Fixed: added invoice_Id (was missing entirely), fixed variable $conn → $pdo
$invoiceId = uniqid('inv_');
$query = "INSERT INTO invoice
            (invoice_Id, brId, IssueDate, amount, supervisorId, saleId,
             policyNumber, paymentFreq, cashHandOverDate, agentSignature, teamLeaderCode)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($query);
$stmt->execute([
    $invoiceId,
    $branchCode,
    $invoiceDate,
    $premiumAmount,
    $supervisorCode,
    $salesAgentCode,
    $policyNumber,
    $paymentFrequent,
    $cashHandedOverDate,
    $agentSignature,
    $teamLeaderCode
]);

if ($stmt->rowCount() > 0) {
    echo json_encode('success');
} else {
    echo json_encode('not success');
}
?>
