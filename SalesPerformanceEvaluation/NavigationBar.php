<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// NavigationBar.php
// NavigationBar.php
// Fixed: removed session_start() - already called in the dashboard files
// Fixed: Dashboard link now stays on the correct dashboard based on role
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
<style>
    .sidebar-nav a { display: block; padding: 10px 20px; color: #bdc3c7; text-decoration: none; border-radius: 5px; margin: 2px 8px; }
    .sidebar-nav a:hover { background: rgba(255,255,255,0.1); color: #fff; }
    .sidebar-nav .sign-out { color: #e74c3c; margin-top: 20px; }
    .sidebar-nav .user-info { padding: 10px 20px; color: #7f8c8d; font-size: 0.8rem; margin-bottom: 10px; border-bottom: 1px solid #3d4e5c; }
</style>
<div class="sidebar-nav">
    <div class="user-info">
        <div><?= htmlspecialchars($empId) ?></div>
        <div><?= htmlspecialchars($role) ?></div>
    </div>
    <a href="<?= $dashboardLink ?>">📊 Dashboard</a>
    <a href="Logout.php" class="sign-out">🚪 Sign Out</a>
</div>