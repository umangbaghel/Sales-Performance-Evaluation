<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$role  = $_SESSION['emp_role'] ?? '';
$empId = $_SESSION['emp_id']   ?? '';
$roleMap = [
    'BranchManager'    => 'DashBoardAdmin.php',
    'Cashier'          => 'DashBoardCashier.php',
    'SalesAgent'       => 'DashBoardSalesAgent.php',
    'InsurenceAdvisor' => 'DashBoardLifeInsurenceAdvisor.php',
    'TeamLead'         => 'DashboardTeamLead.php',
    'Supervisor'       => 'DashboardTeamLead.php',
];
$dashboardLink = $roleMap[$role] ?? 'DashBoardAdmin.php';
?>
<div class="user-info">
    <div class="name"><?= htmlspecialchars($empId) ?></div>
    <div class="role"><?= htmlspecialchars($role) ?></div>
</div>
<div class="sidebar-section">Navigation</div>
<a href="<?= $dashboardLink ?>" class="active">
    <span class="icon">📊</span> Dashboard
</a>
<div class="sidebar-bottom">
    <a href="Logout.php" class="sign-out">
        <span class="icon">🚪</span> Sign Out
    </a>
</div>