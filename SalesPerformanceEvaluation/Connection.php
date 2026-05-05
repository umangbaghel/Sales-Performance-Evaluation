<?php
// Connection.php
// Works both locally (reads .env file) and on Railway (reads environment variables directly)

// Try to load .env file if it exists (for local development)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $k = trim($key);
            $v = trim($value);
            if (!isset($_ENV[$k]) && !getenv($k)) {
                putenv("$k=$v");
                $_ENV[$k] = $v;
            }
        }
    }
}

// Read from environment (works for both .env and Railway variables)
$host     = getenv('DB_HOST')     ?: (getenv('MYSQLHOST')     ?: 'localhost');
$dbname   = getenv('DB_NAME')     ?: (getenv('MYSQLDATABASE') ?: 'sales');
$username = getenv('DB_USER')     ?: (getenv('MYSQLUSER')     ?: 'root');
$password = getenv('DB_PASSWORD') ?: (getenv('MYSQLPASSWORD') ?: '');
$port     = getenv('DB_PORT')     ?: (getenv('MYSQLPORT')     ?: '3306');

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    error_log('DB Connection Error: ' . $e->getMessage());
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}
?>