<p align="center">
  <img src="https://raw.githubusercontent.com/elzidanecodes/rt-administration-frontend/dev/src/assets/logo.png" alt="RT Admin Logo" width="120"/>
</p>

<h1 align="center">RT Administration — Backend API</h1>

<p align="center">
  REST API for residential neighborhood (RT) administration management.<br>
  Handles authentication, house management, resident data, billing, payments, and expense tracking.
</p>

---

## 📋 Table of Contents

1. [About](#-about)
2. [Tech Stack](#-tech-stack)
3. [Requirements](#-requirements)
4. [Step 1 — Install Dependencies](#-step-1--install-dependencies)
5. [Step 2 — Clone Repository](#-step-2--clone-repository)
6. [Step 3 — Install Composer Packages](#-step-3--install-composer-packages)
7. [Step 4 — Environment Configuration](#-step-4--environment-configuration)
8. [Step 5 — Setup Database](#-step-5--setup-database)
9. [Step 6 — Generate Application Key](#-step-6--generate-application-key)
10. [Step 7 — Run Migrations & Seeders](#-step-7--run-migrations--seeders)
11. [Step 8 — Storage Symlink](#-step-8--storage-symlink)
12. [Step 9 — Start Server](#-step-9--start-server)
13. [Step 10 — Test API](#-step-10--test-api)
14. [Default Credentials](#-default-credentials)
15. [Troubleshooting](#-troubleshooting)
16. [API Documentation](#-api-documentation)
17. [Final Checklist](#-final-checklist)
18. [License](#-license)

---

## 📖 About

This system helps RT (residential neighborhood) administrators manage:

- 🏠 20 houses (15 permanent block A + 5 rental block B)
- 👤 Resident data with KTP photo upload
- 💳 Monthly billing (security fee + cleaning fee)
- 💰 Payment recording (monthly or annual)
- 📊 Expense tracking by category
- 📈 Financial reports (income vs expense, 12-month chart)

---

## 🛠️ Tech Stack

| Component | Version | Description |
|-----------|---------|-------------|
| PHP | 8.2+ | Required by Laravel 11 |
| Composer | 2.x | PHP package manager |
| Laravel | 11.x | Backend framework |
| MySQL | 8.0+ | Relational database (MariaDB 10.5+ also supported) |
| Laravel Sanctum | 4.x | Token-based API authentication |

---

## ⚙️ Requirements

Make sure the following are installed before proceeding:

- [ ] **PHP 8.2+**
- [ ] **Composer 2.x**
- [ ] **MySQL 8.0+** or **MariaDB 10.5+**
- [ ] **Git**
- [ ] **PHP Extensions:** `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `pdo_mysql`, `Tokenizer`, `XML`, `cURL`, `GD`

### Verify installed versions:

```bash
php -v          # Must be PHP 8.2.x or higher
composer -V     # Must be Composer version 2.x
mysql --version # Must be MySQL 8.0.x or MariaDB 10.5+
git --version   # Must show output
```

### Verify PHP extensions:

```bash
php -m | grep -E "bcmath|ctype|fileinfo|json|mbstring|openssl|pdo_mysql|tokenizer|xml|curl|gd"
```

All listed extensions must appear in the output.

---

## 📥 Step 1 — Install Dependencies

### PHP 8.2+

**Ubuntu/Debian:**
```bash
sudo apt update && sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd
```

**macOS (Homebrew):**
```bash
brew install php@8.2
brew link php@8.2 --force --overwrite
```

**Windows:**
Download and install [Laragon](https://laragon.org/download/) — includes PHP, MySQL, and Apache.

### Composer

**Linux/macOS:**
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
php -r "unlink('composer-setup.php');"
```

**Windows:**
Download the installer from [getcomposer.org](https://getcomposer.org/download/).

### MySQL 8

**Ubuntu/Debian:**
```bash
sudo apt install mysql-server
sudo mysql_secure_installation
```

**macOS:**
```bash
brew install mysql
brew services start mysql
```

**Windows:**
Already included in Laragon/XAMPP, or download from [mysql.com](https://dev.mysql.com/downloads/installer/).

---

## 📦 Step 2 — Clone Repository

```bash
git clone https://github.com/elzidanecodes/rt-administration-api.git
cd rt-administration-api
```

---

## 🎼 Step 3 — Install Composer Packages

```bash
composer install
```

Expected output:
```
Installing dependencies from lock file
Package operations: 100+ installs, 0 updates, 0 removals
...
Generating optimized autoload files
```

> ⏱️ Estimated time: 2–5 minutes depending on internet connection.

If there are errors about missing PHP extensions, install the listed extension and re-run `composer install`.

---

## 🔧 Step 4 — Environment Configuration

### 4.1 Copy the .env file

```bash
cp .env.example .env
```

**Windows (Command Prompt):**
```cmd
copy .env.example .env
```

### 4.2 Edit `.env`

Open `.env` in a text editor and configure:

```env
APP_NAME="RT Administration"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rt_administration
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173
SESSION_DOMAIN=localhost

FRONTEND_URL=http://localhost:5173

FILESYSTEM_DISK=public
```

**Variables to update:**

| Variable | Default | Change to |
|----------|---------|-----------|
| `DB_HOST` | `127.0.0.1` | Usually unchanged |
| `DB_DATABASE` | `rt_administration` | Match the database name you create in Step 5 |
| `DB_USERNAME` | `root` | Your MySQL username |
| `DB_PASSWORD` | (empty) | Your MySQL password |

---

## 🗄️ Step 5 — Setup Database

### Via Command Line:

```bash
mysql -u root -p
```

Then at the MySQL prompt:
```sql
CREATE DATABASE rt_administration CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Via phpMyAdmin:

1. Open `http://localhost/phpmyadmin`
2. Go to **Databases** tab
3. Create database: name `rt_administration`, collation `utf8mb4_unicode_ci`
4. Click **Create**

---

## 🔑 Step 6 — Generate Application Key

```bash
php artisan key:generate
```

Expected output:
```
INFO  Application key set successfully.
```

---

## 🌱 Step 7 — Run Migrations & Seeders

### Run migrations (creates all tables):

```bash
php artisan migrate
```

Expected: 8+ tables created successfully.

### Run seeders (inserts default data):

```bash
php artisan db:seed
```

**Data inserted:**
- 1 admin user (`rt@perumahan.com` / `password`)
- 20 houses (A-01 to A-15 permanent, B-01 to B-05 rental)
- 5 expense categories (Gaji Satpam, Token Listrik, Perbaikan Jalan, Perbaikan Selokan, Lainnya)

### Reset database (if needed):

```bash
php artisan migrate:fresh --seed
```

> ⚠️ This drops ALL tables and re-creates them. Only use to start fresh.

---

## 🔗 Step 8 — Storage Symlink

```bash
php artisan storage:link
```

Expected output:
```
INFO  The [public/storage] link has been connected to [storage/app/public].
```

This enables uploaded files (KTP photos, receipts) to be accessible via URL.

**If error on Windows:** Run Command Prompt as Administrator and re-run the command.

---

## 🚀 Step 9 — Start Server

```bash
php artisan serve
```

Expected output:
```
INFO  Server running on [http://127.0.0.1:8000].
Press Ctrl+C to stop the server
```

API is available at `http://localhost:8000/api`

> To use a different port: `php artisan serve --port=8001`

---

## ✅ Step 10 — Test API

### Test login via cURL:

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"rt@perumahan.com","password":"password"}'
```

Expected response:
```json
{
  "success": true,
  "data": {
    "user": { "id": 1, "name": "Pak RT", "email": "rt@perumahan.com" },
    "token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ..."
  }
}
```

### Test GET houses (with token):

```bash
curl -X GET http://127.0.0.1:8000/api/houses \
  -H "Accept: application/json" \
  -H "Authorization: Bearer [YOUR_TOKEN]"
```

Should return 20 houses.

---

## 🔐 Default Credentials

| Field | Value |
|-------|-------|
| Email | `rt@perumahan.com` |
| Password | `password` |

> ⚠️ Change the password for any production deployment.

---

## 🛠️ Troubleshooting

| Error | Solution |
|-------|----------|
| `could not find driver` | Install `php8.2-mysql`, restart PHP |
| `SQLSTATE[HY000] [2002] Connection refused` | Start MySQL: `sudo service mysql start` |
| `SQLSTATE[HY000] [1045] Access denied` | Check `DB_USERNAME` and `DB_PASSWORD` in `.env` |
| `Unknown database 'rt_administration'` | Database not created yet — repeat Step 5 |
| `stream or file could not be opened` | Run: `chmod -R 775 storage bootstrap/cache` |
| `419 Page Expired` | Ensure request includes `Accept: application/json` header |
| Port 8000 in use | Use: `php artisan serve --port=8001` |
| `Allowed memory size exhausted` (Composer) | Run: `php -d memory_limit=-1 /usr/local/bin/composer install` |
| `Specified key was too long` | Drop and re-create DB with `utf8mb4_unicode_ci` collation |
| KTP photo returns 404 | Storage symlink missing — repeat Step 8 |

---

## 📚 API Documentation

**Base URL:** `http://127.0.0.1:8000/api`

All endpoints (except `/login`) require:
```
Authorization: Bearer {token}
Accept: application/json
```

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/login` | ❌ | Login admin |
| POST | `/api/logout` | ✅ | Logout |
| GET | `/api/me` | ✅ | Get current user |
| GET | `/api/houses` | ✅ | List houses |
| POST | `/api/houses` | ✅ | Create house |
| GET | `/api/houses/{id}` | ✅ | House detail + history |
| PUT | `/api/houses/{id}` | ✅ | Update house |
| DELETE | `/api/houses/{id}` | ✅ | Delete house |
| POST | `/api/houses/{id}/assign-resident` | ✅ | Assign resident |
| POST | `/api/houses/{id}/unassign-resident` | ✅ | Unassign resident |
| GET | `/api/residents` | ✅ | List residents |
| POST | `/api/residents` | ✅ | Create resident (with KTP) |
| GET | `/api/residents/{id}` | ✅ | Resident detail |
| PUT | `/api/residents/{id}` | ✅ | Update resident |
| DELETE | `/api/residents/{id}` | ✅ | Delete resident |
| GET | `/api/bills` | ✅ | List bills |
| POST | `/api/bills/generate` | ✅ | Generate monthly bills |
| GET | `/api/bills/{id}` | ✅ | Bill detail |
| GET | `/api/payments` | ✅ | List payments |
| POST | `/api/payments` | ✅ | Record payment |
| POST | `/api/payments/pay-annual` | ✅ | Pay 12 months at once |
| GET | `/api/expense-categories` | ✅ | List categories |
| POST | `/api/expense-categories` | ✅ | Create category |
| PUT | `/api/expense-categories/{id}` | ✅ | Update category |
| DELETE | `/api/expense-categories/{id}` | ✅ | Delete category |
| GET | `/api/expenses` | ✅ | List expenses |
| POST | `/api/expenses` | ✅ | Create expense |
| GET | `/api/expenses/{id}` | ✅ | Expense detail |
| PUT | `/api/expenses/{id}` | ✅ | Update expense |
| DELETE | `/api/expenses/{id}` | ✅ | Delete expense |
| GET | `/api/reports/annual-chart` | ✅ | 12-month chart data |
| GET | `/api/reports/monthly-detail` | ✅ | Monthly breakdown |
| GET | `/api/reports/dashboard-summary` | ✅ | Dashboard summary |

**Total: 33 endpoints**

---

## ✅ Final Checklist

Before moving to the frontend, verify all items:

- [ ] PHP 8.2+ installed (`php -v`)
- [ ] Composer installed (`composer -V`)
- [ ] MySQL 8+ installed and running
- [ ] Repository cloned
- [ ] `composer install` completed without errors
- [ ] `.env` file configured (DB credentials)
- [ ] Database `rt_administration` created
- [ ] `php artisan key:generate` succeeded
- [ ] `php artisan migrate` succeeded (8+ tables created)
- [ ] `php artisan db:seed` succeeded (20 houses + 1 user + 5 categories)
- [ ] `php artisan storage:link` succeeded
- [ ] `php artisan serve` running at `http://localhost:8000`
- [ ] Login via cURL/Postman returns a token
- [ ] `GET /api/houses` returns 20 houses

If all ✅, proceed to install the frontend → [rt-administration-frontend](https://github.com/elzidanecodes/rt-administration-frontend)

---

## 📜 License

&copy; 2025 Laita Zidan  
Released under the [MIT License](LICENSE)

---

## 🙋 About the Developer

**Laita Zidan**  
Program Studi Sistem Informasi Bisnis  
Politeknik Negeri Malang (POLINEMA)  
GitHub: [github.com/elzidanecodes](https://github.com/elzidanecodes)
