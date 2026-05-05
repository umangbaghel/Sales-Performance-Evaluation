# How to Run — Sales Performance Evaluation System
# macOS 12 (No Docker, No MySQL 8.0 needed)

---

## Option A — MAMP (Easiest, Recommended)

MAMP is a free app that installs Apache + PHP + MySQL in one click.
It works perfectly on macOS 12.

### Step 1 — Install MAMP
Download free version from: https://www.mamp.info/en/downloads/
Install and open MAMP (not MAMP PRO).

### Step 2 — Put your project in the right folder
Copy the entire `SalesPerformanceEvaluation/` folder into:
```
/Applications/MAMP/htdocs/SalesPerformanceEvaluation/
```

### Step 3 — Set up the .env file
```bash
cd /Applications/MAMP/htdocs/
cp .env.example .env
```
Edit `.env` — for MAMP the defaults are:
```
DB_HOST=localhost
DB_NAME=sales
DB_USER=root
DB_PASSWORD=root      ← MAMP default password is "root"
DB_PORT=3306
```

### Step 4 — Create the database
Open your browser and go to:
```
http://localhost:8888/phpMyAdmin
```
- Click "New" in the left sidebar
- Database name: `sales`
- Collation: `utf8mb4_general_ci`
- Click Create

### Step 5 — Import the SQL
In phpMyAdmin:
- Click on the `sales` database
- Click "Import" tab
- Choose file: `SalesPerformanceEvaluation/sales_performance.sql`
- Click "Go"

### Step 6 — Start the app
Go to: http://localhost:8888/SalesPerformanceEvaluation/Index.php

---

## Option B — Homebrew (No extra app needed)

### Step 1 — Install Homebrew (if not already installed)
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### Step 2 — Install PHP and MySQL 5.7
MySQL 5.7 works on macOS 12 even though 8.0 doesn't.
```bash
brew install php
brew install mysql@5.7
brew link mysql@5.7 --force
brew services start mysql@5.7
```

### Step 3 — Set up MySQL root password (first time)
```bash
mysql_secure_installation
# Follow prompts — you can press Enter to skip most
# Set a root password or leave blank
```

### Step 4 — Create the database and import SQL
```bash
mysql -u root -p -e "CREATE DATABASE sales CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root -p sales < /path/to/SalesPerformanceEvaluation/sales_performance.sql
```

### Step 5 — Set up .env
```bash
cp .env.example .env
# Edit .env and set DB_PASSWORD to whatever you set in step 3
```

### Step 6 — Start the PHP server
```bash
cd /path/to/SalesPerformanceEvaluation
php -S localhost:8000
```

### Step 7 — Open the app
Go to: http://localhost:8000/Index.php

---

## Login Credentials (from the database seed)

| Employee Code | Password       | Role                  |
|---------------|----------------|-----------------------|
| 00001         | Agent@123      | Sales Agent           |
| 00002         | Advisor@123    | Insurance Advisor     |
| 00003         | Lead@123       | Team Lead             |
| 00004         | Supervisor@123 | Supervisor            |
| 00005         | Manager@123    | Branch Manager/Admin  |
| 00006         | Cashier@123    | Cashier               |

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| "Connection failed" error | Check your .env — DB_PASSWORD must match what you set in MySQL |
| "Database not found" | Make sure you created the `sales` DB and imported the SQL |
| Port 8000 in use | Use `php -S localhost:8001` instead |
| White page / no output | Run `php -l Index.php` to check for syntax errors |
| phpMyAdmin login fails (MAMP) | Username: root, Password: root |
