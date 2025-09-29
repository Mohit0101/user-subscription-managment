<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## User & Subscription Management REST API

A Laravel 12 based REST API for managing user authentication, subscriptions, and plans with API versioning (v1/v2).
It uses Laravel Passport for authentication, the Repository + Service pattern, and includes admin reports.

## 🚀 Features

- 🔑 Authentication (Passport OAuth2)
    - Register / Login / Logout
    - Profile (/me)
    - Token-based authentication
- 👤 User
    - Subscribe / Cancel subscription
    - View active subscription
- 🛠 Admin
  - Manage subscription plans (CRUD)
  - View reports on user subscriptions
- 🎁 Promo Codes
    - Available in v2 subscription endpoints
- 📂 Architecture
    - Repository + Service pattern
    - API versioning (v1 and v2)

## 🚀 Getting Started

Follow these steps to set up the project locally:

### 1️⃣ Clone the repository
```bash
git clone https://github.com/yourname/user-subscription-management.git
cd user-subscription-management
```

### 2️⃣ Install dependencies
```bash
composer install
```

### 3️⃣ Set up environment
```bash
APP_NAME=Laravel
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=user_subscription_management
DB_USERNAME=sail
DB_PASSWORD=password
```

### 4️⃣ Run Sail (Docker)
```bash
./vendor/bin/sail up -d
```

### 5️⃣ Run migrations & seeders
```bash
./vendor/bin/sail artisan migrate --seed
```

### 6️⃣ Install Passport
```bash
./vendor/bin/sail artisan passport:install
```

### Authentication

This project uses Laravel Passport with Bearer tokens.
Every protected route requires the header:

```bash
Authorization: Bearer <access_token>
Accept: application/json
```

## 📡 API Endpoints

### **Auth (v1)**
| Method | Endpoint                | Description           |
|--------|-------------------------|-----------------------|
| POST   | `/api/v1/auth/register` | Register user         |
| POST   | `/api/v1/auth/login`    | Login (get token)     |
| GET    | `/api/v1/auth/me`       | Get logged-in user    |
| POST   | `/api/v1/auth/logout`   | Logout (revoke token) |

---

### **Subscriptions (v1)**
| Method | Endpoint                           | Description          |
|--------|------------------------------------|----------------------|
| POST   | `/api/v1/subscriptions/subscribe`  | Subscribe to a plan  |
| POST   | `/api/v1/subscriptions/cancel`     | Cancel subscription  |
| GET    | `/api/v1/subscriptions/active`     | Get active plan      |

---

### **Admin (v1)**
| Method | Endpoint                                   | Description              |
|--------|--------------------------------------------|--------------------------|
| GET    | `/api/v1/admin/plans`                      | List all plans           |
| POST   | `/api/v1/admin/plans`                      | Create new plan          |
| PUT    | `/api/v1/admin/plans/{plan}`               | Update plan              |
| DELETE | `/api/v1/admin/plans/{plan}`               | Delete plan              |
| GET    | `/api/v1/admin/reports/user-subscriptions` | User subscription report |

---

### **Subscriptions (v2)**
| Method | Endpoint                           | Description                  |
|--------|------------------------------------|------------------------------|
| POST   | `/api/v2/subscriptions/subscribe`  | Subscribe (supports promos)  |


## 📊 Database Schema (Simplified ERD)

- users (id, name, email, password, role)
- plans (id, name, price, interval)
- subscriptions (id, user_id, plan_id, status, started_at, ended_at)
- promo_codes (id, code, discount, expires_at)

## 🧪 Example Usage

Login

```bash
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "Secret123!"
}
```
Response:
```bash
{
  "token": "eyJ0eXAiOiJKV1QiLCJh..."
  "user": {
        "id": 1,
        "name": "abc",
        "email": "abc@example.com",
        "email_verified_at": null,
        "created_at": "2025-09-18T21:41:01.000000Z",
        "updated_at": "2025-09-19T04:33:38.000000Z",
        "role": "user"
    }
}
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
