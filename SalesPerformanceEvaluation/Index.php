<!DOCTYPE html>
<html>
<head>
    <title>Sales Performance Evaluation - Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        body { background: #f8f9fa; }
        .login-card {
            max-width: 420px;
            margin: 100px auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.10);
        }
        .login-card h1 { font-size: 1.5rem; margin-bottom: 1.5rem; text-align: center; }
        #errorMsg { display: none; }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>Sales Performance System</h1>
        <form id="loginForm">
            <div class="form-group">
                <label for="agentCode">Employee Code</label>
                <input type="text" class="form-control" id="agentCode" name="agentCode" placeholder="e.g. 00001" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <div id="errorMsg" class="alert alert-danger mt-3"></div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#loginForm").on('submit', function (e) {
                e.preventDefault();
                $('#errorMsg').hide();

                $.ajax({
                    url: 'Login.php',   // Fixed: was 'login.php' — macOS is case-sensitive
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (data) {
                        // Fixed: all dashboard filenames now match actual files on disk
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
                            $('#errorMsg').text('Invalid credentials. Please try again.').show();
                        }
                    },
                    error: function () {
                        $('#errorMsg').text('Server error. Please try again.').show();
                    }
                });
            });
        });
    </script>
</body>
</html>
