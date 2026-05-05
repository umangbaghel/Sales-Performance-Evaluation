<?php
// Supabase PostgreSQL Connection for Sales Performance System
// Get credentials from https://supabase.com → Project Settings → Database

// ==================== CONFIGURATION ====================
// Fill these with your Supabase credentials

$host = getenv('DB_HOST') ?: 'db.xxxxx.supabase.co';        // Get from Supabase Settings
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'postgres';
$username = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: '';  // Your Supabase database password

// ==================== CONNECTION ====================

try {
    // PostgreSQL connection string for Supabase
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    
    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Test connection
    $pdo->query("SELECT 1");
    
} catch (PDOException $e) {
    // Connection failed
    error_log('Supabase Connection Error: ' . $e->getMessage());
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

// ==================== HELPFUL NOTES ====================
/*
 * SETUP INSTRUCTIONS:
 * 
 * 1. Create Supabase Account: https://supabase.com
 * 
 * 2. Create New Project
 *    - Project name: sales-performance
 *    - Database password: (save this!)
 *    - Region: Choose closest to you
 * 
 * 3. Get Credentials:
 *    - Go to Project Settings → Database
 *    - Host: db.xxxxx.supabase.co
 *    - Port: 5432
 *    - Database: postgres
 *    - User: postgres
 *    - Password: (your password)
 * 
 * 4. Update Configuration Above with your credentials
 * 
 * 5. Run SQL Schema:
 *    - In Supabase: SQL Editor → New Query
 *    - Paste: sales_performance_supabase.sql
 *    - Click Run
 * 
 * 6. Test Connection:
 *    php -r "include 'Connection_Supabase.php'; echo 'Connected!'"
 * 
 * ADVANTAGES:
 * ✓ No server management needed
 * ✓ Automatic backups (daily)
 * ✓ Easy scaling
 * ✓ Free tier (500MB, perfect for dev)
 * ✓ Enterprise security
 * ✓ Real-time capabilities
 * 
 * ENVIRONMENT VARIABLES (Recommended):
 * 
 * Create .env file in project root:
 * 
 *   DB_HOST=db.xxxxx.supabase.co
 *   DB_PORT=5432
 *   DB_NAME=postgres
 *   DB_USER=postgres
 *   DB_PASSWORD=your_password_here
 * 
 * Then use:
 *   $host = getenv('DB_HOST');
 *   $password = getenv('DB_PASSWORD');
 *   etc...
 * 
 * TROUBLESHOOTING:
 * 
 * Connection refused?
 *   - Check host URL from Supabase Settings
 *   - Verify password is correct
 * 
 * SSL error?
 *   - Supabase requires SSL
 *   - Add to connection string:
 *     $dsn .= ";sslmode=require";
 * 
 * Table not found?
 *   - Re-run sales_performance_supabase.sql
 *   - Check in Supabase SQL Editor that tables exist
 * 
 * SWITCHING BETWEEN MySQL AND Supabase:
 * 
 * Method 1: Replace Connection.php
 *   cp Connection_Supabase.php Connection.php
 * 
 * Method 2: Use in code
 *   // Include Supabase instead of MySQL
 *   include 'Connection_Supabase.php';  // Supabase (PostgreSQL)
 *   // include 'Connection.php';        // MySQL
 * 
 * DATABASE COMPARISON:
 * 
 * MySQL Query:
 *   SELECT CONCAT(emp_id, '-', emp_role) FROM employee;
 * 
 * PostgreSQL Query:
 *   SELECT emp_id || '-' || emp_role FROM employee;
 * 
 * Most queries work the same - just watch string functions!
 * 
 */

?>
