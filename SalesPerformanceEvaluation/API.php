<?php
// API.php - Sales Performance REST API
// Access: http://localhost:8000/API.php?action=login&code=00001&password=Agent@123

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

include 'Connection.php';

// Get action from request
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

// API Response function
function response($success, $message, $data = null) {
    return json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
}

try {
    switch($action) {
        // ==================== LOGIN ====================
        case 'login':
            $code = $_POST['code'] ?? $_GET['code'] ?? null;
            $password = $_POST['password'] ?? $_GET['password'] ?? null;
            
            if(!$code || !$password) {
                echo response(false, 'Code and password required');
                break;
            }
            
            $stmt = $pdo->prepare("SELECT * FROM employee WHERE emp_id = ? AND emp_password = ?");
            $stmt->execute([$code, $password]);
            $employee = $stmt->fetch();
            
            if($employee) {
                echo response(true, 'Login successful', [
                    'emp_id' => $employee['emp_id'],
                    'role' => $employee['emp_role'],
                    'branch' => $employee['br_id']
                ]);
            } else {
                echo response(false, 'Invalid credentials');
            }
            break;

        // ==================== GET ALL EMPLOYEES ====================
        case 'employees':
            $stmt = $pdo->query("SELECT emp_id, emp_role, br_id FROM employee");
            $employees = $stmt->fetchAll();
            echo response(true, 'Employees retrieved', $employees);
            break;

        // ==================== GET EMPLOYEE DETAILS ====================
        case 'employee_details':
            $emp_id = $_GET['emp_id'] ?? null;
            if(!$emp_id) {
                echo response(false, 'emp_id required');
                break;
            }
            
            $stmt = $pdo->prepare("SELECT * FROM employee WHERE emp_id = ?");
            $stmt->execute([$emp_id]);
            $employee = $stmt->fetch();
            
            if($employee) {
                echo response(true, 'Employee found', $employee);
            } else {
                echo response(false, 'Employee not found');
            }
            break;

        // ==================== GET SALES DATA ====================
        case 'sales':
            $emp_id = $_GET['emp_id'] ?? null;
            
            $query = "SELECT * FROM totalsales WHERE 1=1";
            $params = [];
            
            if($emp_id) {
                $query .= " AND emp_id = ?";
                $params[] = $emp_id;
            }
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $sales = $stmt->fetchAll();
            
            echo response(true, 'Sales data retrieved', $sales);
            break;

        // ==================== CREATE SALE ====================
        case 'create_sale':
            $data = json_decode(file_get_contents("php://input"), true);
            
            if(!isset($data['sale_name']) || !isset($data['emp_id']) || !isset($data['br_id'])) {
                echo response(false, 'Missing required fields: sale_name, emp_id, br_id');
                break;
            }
            
            $sale_id = uniqid('sale_');
            $stmt = $pdo->prepare("INSERT INTO totalsales (sale_id, sale_name, emp_id, br_id) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$sale_id, $data['sale_name'], $data['emp_id'], $data['br_id']]);
            
            if($result) {
                echo response(true, 'Sale created', ['sale_id' => $sale_id]);
            } else {
                echo response(false, 'Failed to create sale');
            }
            break;

        // ==================== GET PERFORMANCE ====================
        case 'performance':
            $emp_id = $_GET['emp_id'] ?? null;
            
            $query = "SELECT * FROM performance WHERE 1=1";
            $params = [];
            
            if($emp_id) {
                $query .= " AND emp_id = ?";
                $params[] = $emp_id;
            }
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $performance = $stmt->fetchAll();
            
            echo response(true, 'Performance data retrieved', $performance);
            break;

        // ==================== GET TARGETS ====================
        case 'targets':
            $br_id = $_GET['br_id'] ?? null;
            
            $query = "SELECT * FROM target WHERE 1=1";
            $params = [];
            
            if($br_id) {
                $query .= " AND br_id = ?";
                $params[] = $br_id;
            }
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $targets = $stmt->fetchAll();
            
            echo response(true, 'Targets retrieved', $targets);
            break;

        // ==================== GET BRANCHES ====================
        case 'branches':
            $stmt = $pdo->query("SELECT * FROM branch");
            $branches = $stmt->fetchAll();
            echo response(true, 'Branches retrieved', $branches);
            break;

        // ==================== GET CASHIERS ====================
        case 'cashiers':
            $stmt = $pdo->query("SELECT * FROM cashier");
            $cashiers = $stmt->fetchAll();
            echo response(true, 'Cashiers retrieved', $cashiers);
            break;

        // ==================== HEALTH CHECK ====================
        case 'health':
            echo response(true, 'API is healthy', [
                'status' => 'running',
                'database' => 'connected',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            break;

        default:
            echo response(false, 'Invalid action. Available actions: login, employees, employee_details, sales, create_sale, performance, targets, branches, cashiers, health');
    }
} catch(Exception $e) {
    echo response(false, 'Error: ' . $e->getMessage());
}
?>
