<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sales Performance System</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: 'Inter', -apple-system, sans-serif;
    background: #ffffff;
    color: #1f2937;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.wrap {
    width: 100%;
    max-width: 360px;
    padding: 24px;
    animation: fadeUp 0.4s ease;
}
.brand {
    text-align: center;
    margin-bottom: 28px;
}
.brand-icon {
    width: 200px; height: 172px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
}
.brand-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
}
.brand h1 {
    font-size: 1.65rem;
    font-weight: 600;
    color: #1f2937;
    letter-spacing: -0.3px;
}
.brand h1 span {
    color: #ffffff;
    background: #4493f8;
    padding: 4px 12px;
    border-radius: 6px;
}
.brand p { color: #64748b; font-size: 0.8rem; margin-top: 4px; }
.card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 24px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
}
.form-group { margin-bottom: 16px; }
.form-group label {
    display: block;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #64748b;
    margin-bottom: 6px;
}
.form-control {
    width: 100%;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 9px 12px;
    color: #1f2937;
    font-family: 'Inter', sans-serif;
    font-size: 0.875rem;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.form-control:focus {
    border-color: #4493f8;
    box-shadow: 0 0 0 3px rgba(68,147,248,0.1);
}
.btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 10px 18px;
    background: #4493f8;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.15s;
    margin-top: 4px;
    line-height: 1.5;
    min-height: 40px;
}
.btn:hover { background: #3a82e0; box-shadow: 0 0 0 3px rgba(68,147,248,0.2); }
.btn:disabled { opacity: 0.6; cursor: not-allowed; }
.divider { border:none; border-top: 1px solid #e2e8f0; margin: 20px 0; }
.hint { text-align: center; font-size: 0.75rem; color: #64748b; }
.error-msg {
    margin-top: 12px;
    padding: 10px 12px;
    background: rgba(248,81,73,0.08);
    border: 1px solid rgba(248,81,73,0.2);
    border-radius: 6px;
    color: #f85149;
    font-size: 0.8rem;
    display: none;
}
@keyframes fadeUp {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>
</head>
<body>
<div class="wrap">
    <div class="brand">
        <div class="brand-icon">
            <img src="sales-performance-logo.png" alt="Sales Performance logo">
        </div>
        <h1><span>Sales Performance</span></h1>
        <p>Sign in to your dashboard</p>
    </div>
    <div class="card">
        <form id="loginForm">
            <div class="form-group">
                <label>Employee Code</label>
                <input type="text" class="form-control" name="agentCode" placeholder="Enter Code" autocomplete="username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter Password" autocomplete="current-password">
            </div>
            <button type="submit" class="btn" id="loginBtn">Sign In</button>
            <div class="error-msg" id="errorMsg"></div>
        </form>
        <hr class="divider">
        <p class="hint">Contact your branch manager if you need access.</p>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#loginForm').on('submit', function(e) {
    e.preventDefault();
    var btn = $('#loginBtn');
    btn.prop('disabled', true).text('Signing in...');
    $('#errorMsg').hide();
    $.ajax({
        url: 'Login.php', type: 'POST', data: $(this).serialize(),
        success: function(data) {
            var routes = {
                'admin':'DashBoardAdmin.php','manager':'DashBoardManager.php',
                'cashier':'DashBoardCashier.php','salesAgent':'DashBoardSalesAgent.php',
                'insurance':'DashBoardLifeInsurenceAdvisor.php',
                'tleader':'DashboardTeamLead.php','supervisor':'DashboardTeamLead.php'
            };
            if (routes[data]) { window.location.href = routes[data]; }
            else { btn.prop('disabled',false).text('Sign In'); $('#errorMsg').text('Invalid credentials. Please try again.').show(); }
        },
        error: function() { btn.prop('disabled',false).text('Sign In'); $('#errorMsg').text('Server error. Please try again.').show(); }
    });
});
</script>
</body>
</html>
