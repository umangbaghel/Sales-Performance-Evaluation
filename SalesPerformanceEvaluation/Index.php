<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Performance System</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0e1a;
            --surface: #141d35;
            --surface2: #1a2540;
            --border: rgba(255,255,255,0.06);
            --accent: #4f8aff;
            --text: #e8edf8;
            --text-muted: #6b7a99;
            --danger: #ff4d6d;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            top: -30%; left: -20%;
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(79,138,255,0.08) 0%, transparent 65%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -30%; right: -20%;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(124,92,252,0.06) 0%, transparent 65%);
            pointer-events: none;
        }
        .login-wrap {
            width: 100%;
            max-width: 420px;
            padding: 24px;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.5s ease;
        }
        .brand {
            text-align: center;
            margin-bottom: 40px;
        }
        .brand-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--accent), #7c5cfc);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 16px;
            box-shadow: 0 8px 32px rgba(79,138,255,0.3);
        }
        .brand h1 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            letter-spacing: -0.5px;
            color: var(--text);
        }
        .brand p {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-top: 4px;
        }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79,138,255,0.1);
        }
        .btn {
            width: 100%;
            padding: 13px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 4px;
            letter-spacing: 0.3px;
        }
        .btn:hover {
            background: #3a73e8;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(79,138,255,0.3);
        }
        .btn:active { transform: translateY(0); }
        .btn.loading { opacity: 0.7; pointer-events: none; }
        .error-msg {
            margin-top: 16px;
            padding: 12px 16px;
            background: rgba(255,77,109,0.08);
            border: 1px solid rgba(255,77,109,0.2);
            border-radius: 8px;
            color: var(--danger);
            font-size: 0.85rem;
            display: none;
        }
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 24px 0;
        }
        .hint {
            text-align: center;
            font-size: 0.78rem;
            color: var(--text-muted);
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(20px); }
            to   { opacity:1; transform:translateY(0); }
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="brand">
        <div class="brand-icon">📊</div>
        <h1>Sales Performance</h1>
        <p>Sign in to your dashboard</p>
    </div>
    <div class="card">
        <form id="loginForm">
            <div class="form-group">
                <label>Employee Code</label>
                <input type="text" class="form-control" id="agentCode" name="agentCode" placeholder="e.g. 00005" autocomplete="username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" autocomplete="current-password">
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
$(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        $('#loginBtn').addClass('loading').text('Signing in...');
        $('#errorMsg').hide();
        $.ajax({
            url: 'Login.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(data) {
                var routes = {
                    'admin':      'DashBoardAdmin.php',
                    'manager':    'DashBoardManager.php',
                    'cashier':    'DashBoardCashier.php',
                    'salesAgent': 'DashBoardSalesAgent.php',
                    'insurance':  'DashBoardLifeInsurenceAdvisor.php',
                    'tleader':    'DashboardTeamLead.php',
                    'supervisor': 'DashboardTeamLead.php'
                };
                if (routes[data]) {
                    window.location.href = routes[data];
                } else {
                    $('#loginBtn').removeClass('loading').text('Sign In');
                    $('#errorMsg').text('Invalid credentials. Please try again.').show();
                }
            },
            error: function() {
                $('#loginBtn').removeClass('loading').text('Sign In');
                $('#errorMsg').text('Server error. Please try again.').show();
            }
        });
    });
});
</script>
</body>
</html>