<p align="center">
  <img src="https://raw.githubusercontent.com/elzidanecodes/rt-administration-frontend/dev/src/assets/logo.png" alt="RT Admin Logo" width="120"/>
</p>

<h1 align="center">RT Administration — Backend API</h1>

<p align="center">
  REST API for RT (Residential Neighborhood) administration management system.<br>
  Built by <strong>Laita Zidan</strong>.<br>
  Handles authentication, house management, resident data, billing, payments, and expense tracking.
</p>

---

## 🎯 Features

- Token-based authentication with Laravel Sanctum
- Houses CRUD with assign / unassign resident
- Residents CRUD with KTP photo upload
- Bills management with partial payment support
- Expense tracking by category
- Dashboard summary & annual financial chart data
- Paginated, filterable, and searchable list endpoints

---

## 🧠 Tech Stack

| Component | Description |
|-----------|-------------|
| **Laravel 11** | PHP framework (PHP 8.2+) |
| **MySQL** | Relational database |
| **Laravel Sanctum** | SPA token-based authentication |
| **Eloquent ORM** | Database interaction |
| **Laravel Resource** | API response transformation |

---

## 🧩 Directory Structure

```
rt-administration-api/
├── app/
│   ├── Http/
│   │   └── Controllers/Api/   # AuthController, HouseController, ResidentController
│   └── Models/                # House, Resident, Bill, Payment, Expense, etc.
├── database/
│   ├── migrations/            # All table definitions
│   └── seeders/               # Default users, houses, expense categories
├── routes/
│   └── api.php                # All API route definitions
└── storage/app/public/        # Uploaded files (KTP photos, receipts)
```

---

## 🔁 API Endpoints

### Auth
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/login` | Login and get token |
| POST | `/api/logout` | Logout (revoke token) |
| GET | `/api/me` | Get authenticated user |

### Houses
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/houses` | List houses (paginated, filterable) |
| POST | `/api/houses` | Create house |
| GET | `/api/houses/{id}` | Get house detail |
| PUT | `/api/houses/{id}` | Update house |
| DELETE | `/api/houses/{id}` | Delete house |
| POST | `/api/houses/{id}/assign-resident` | Assign resident to house |
| POST | `/api/houses/{id}/unassign-resident` | Unassign resident from house |

### Residents
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/residents` | List residents (paginated, filterable) |
| POST | `/api/residents` | Create resident |
| GET | `/api/residents/{id}` | Get resident detail |
| PUT | `/api/residents/{id}` | Update resident |
| DELETE | `/api/residents/{id}` | Delete resident |

> All endpoints except `/login` require `Authorization: Bearer {token}` header.

---

## 🚀 Getting Started

**1. Clone & install dependencies**
```bash
git clone https://github.com/elzidanecodes/rt-administration-api.git
cd rt-administration-api
composer install
```

**2. Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

Configure database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rt_administration
DB_USERNAME=root
DB_PASSWORD=
```

**3. Run migrations & seeders**
```bash
php artisan migrate --seed
```

**4. Create storage symlink**
```bash
php artisan storage:link
```

**5. Start the server**
```bash
php artisan serve
```

API will be available at `http://localhost:8000/api`

---

## 👤 Default Credentials

```
Email:    admin@rt.local
Password: password
```

---

## 👮 Roles & Access

| Role | Access |
|------|--------|
| Admin | Full access to all endpoints |

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
