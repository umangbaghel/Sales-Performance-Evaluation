<?php
// NavigationBar.php
// Fixed: Sign Out was a dead '#' link — now points to Logout.php
// Fixed: include path for bootstrap is now inline (no extra file needed)
session_start();
$role = $_SESSION['emp_role'] ?? 'Guest';
$empId = $_SESSION['emp_id'] ?? '';
?>
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link active" href="Index.php">
            <strong>Dashboard</strong>
        </a>
    </li>
    <li class="nav-item">
        <span class="nav-link text-muted" style="font-size:0.85rem;">
            Logged in as: <strong><?= htmlspecialchars($empId) ?></strong><br>
            Role: <strong><?= htmlspecialchars($role) ?></strong>
        </span>
    </li>
    <li class="nav-item mt-3">
        <a class="nav-link text-danger" href="Logout.php">Sign Out</a>
    </li>
</ul>
