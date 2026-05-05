<?php
// Login.php - Handles login POST from Index.php
// Fixed: was using wrong column names (empId, empRole) — now uses emp_Id, emp_Role
// Fixed: now starts a session and stores user info so dashboards can check auth
// Fixed: include path was 'connection.php' (lowercase) — inconsistent casing on macOS

session_start();
include_once 'Connection.php';

header('Content-Type: application/json');

$empId    = isset($_POST['agentCode']) ? trim($_POST['agentCode']) : '';
$password = isset($_POST['password'])  ? trim($_POST['password'])  : '';

if (empty($empId) || empty($password)) {
    echo json_encode('');
    exit;
}

// Fixed: column names corrected to match the CREATE TABLE definition
$stmt = $pdo->prepare("SELECT emp_Id, emp_Role, br_Id FROM employee WHERE emp_Id = ? AND emp_Password = ?");
$stmt->execute([$empId, $password]);
$user = $stmt->fetch();

if ($user) {
    // Store session so dashboards know who is logged in
    $_SESSION['emp_id']   = $user['emp_Id'];
    $_SESSION['emp_role'] = $user['emp_Role'];
    $_SESSION['br_id']    = $user['br_Id'];

    $role = strtolower($user['emp_Role']);

    // Map DB role → response string that Index.php JS already expects
    $roleMap = [
        'branchmanager'    => 'admin',
        'branchmanageradmin' => 'admin',
        'cashier'          => 'cashier',
        'salesagent'       => 'salesAgent',
        'insurenceadvisor' => 'insurance',
        'teamlead'         => 'tleader',
        'supervisor'       => 'supervisor',
    ];

    echo json_encode($roleMap[$role] ?? 'admin');
} else {
    echo json_encode('Invalid credentials');
}
?>
