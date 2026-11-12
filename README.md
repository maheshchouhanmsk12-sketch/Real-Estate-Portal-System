# 🏠 Real Estate Portal System (REPS)

Production-ready, Dockerized web application built with **PHP 8.2**, **Apache**, **Aiven Cloud MySQL**, and **AdminLTE 3**, optimized for high-performance deployment on **Render**.

---

## 🌐 Live URLs & Endpoints

| Portal | Endpoint URL | Description |
| :--- | :--- | :--- |
| **Public Website** | [Open Homepage](https://real-estate-portal-system.onrender.com/) | Real estate listings, property search, and amenities overview |
| **Admin Login** | [Open Admin Login](https://real-estate-portal-system.onrender.com/admin/login.php) | System administrator control panel & user management |
| **Agent Login** | [Open Agent Login](https://real-estate-portal-system.onrender.com/agent/login.php) | Real estate agent portal for managing property listings |
| **1-Click DB Setup** | [Open DB Setup Tool](https://real-estate-portal-system.onrender.com/db_setup.php) | Cloud MySQL Health Check & Auto-Migrator (Aiven SSL Ready) |

---

## 🔑 Default Credentials

### 1. System Administrator
- **Login URL:** `/admin/login.php`
- **Username:** `admin`
- **Password:** `admin123`
- **Role:** Full System Administrator

### 2. Demo Real Estate Agent
- **Login URL:** `/agent/login.php`
- **Email:** `mcooper@sample.com`
- **Password:** `cooper123` *(or register a new agent at `/agent/registration.php`)*
- **Role:** Real Estate Agent

---

## ⚙️ Environment Variables (Render Dashboard)

Configure these in your Render Web Service dashboard under **Environment**:

| Variable | Recommended / Example Value | Notes |
| :--- | :--- | :--- |
| `DB_SERVER` | `mysql-xxxxxx.aivencloud.com` | Your Aiven MySQL service host |
| `DB_PORT` | `26284` | Your Aiven 5-digit custom port |
| `DB_USERNAME` | `avnadmin` | Default Aiven MySQL user |
| `DB_PASSWORD` | *(Your Aiven Cloud Password)* | Found in your Aiven Console Overview |
| `DB_NAME` | `defaultdb` | Default database name on Aiven |
| `DB_SSL` | `true` | Required for Aiven SSL/TLS encryption |
| `BASE_URL` | `https://real-estate-portal-system.onrender.com/` | Your live Render domain with trailing slash |

---

## 🚀 1-Click Database Setup (Aiven Cloud)

1. Deploy the project on **Render** (via Docker or Blueprint `render.yaml`).
2. Add your Aiven credentials under the **Environment** tab in Render.
3. Open `https://<your-render-app>.onrender.com/db_setup.php`.
4. Click **"1-Click Import Database to Aiven Now"**.
5. The migrator automatically disables `FOREIGN_KEY_CHECKS`, parses `reps_db.sql`, verifies all 8 tables, and seeds the default administrator account.
6. Click **"Go to Admin Login"** and sign in with `admin` / `admin123`.

---

## 🛠️ Architecture & Compatibility Enhancements

1. **PHP 8.2 & Apache Docker Container:**
   - Production Dockerfile configured with `php:8.2-apache`, enabling `mysqli`, `pdo_mysql`, `gd`, `zip`, `mod_rewrite`, and `headers`.
2. **Aiven Cloud SSL Security:**
   - Enforces `MYSQLI_CLIENT_SSL` via `mysqli_init()` and `ssl_set()` to satisfy Aiven's mandatory `require_secure_transport=ON`.
3. **Render Reverse-Proxy Fix (Port 10000 Leak Prevention):**
   - Configured `UseCanonicalName Off`, `UseCanonicalPhysicalPort Off`, and `ServerName localhost` in Apache.
   - Redirects explicitly route to direct file endpoints (`admin/login.php`, `admin/index.php`, `agent/login.php`, `agent/index.php`), eliminating directory trailing slash port 10000 lockups.
4. **PHP 8.x Safe Property Initializations:**
   - Class properties initialized inside `__construct()`, preventing PHP 8.x fatal reflection/constant expression errors.
5. **Fail-Safe Authentication & Query Protection:**
   - Dual-mode authentication (AJAX + synchronous POST fallback) with real-time database connection diagnostics.
   - Null-checks and `mysqli_result` validation prevent fatal crashes when tables are not yet initialized.

---

## 📁 Repository Structure

```text
├── Dockerfile                  # Production container definition (PHP 8.2 + Apache)
├── render.yaml                 # Render Blueprint configuration
├── start.sh                    # Dynamic Apache $PORT startup & reverse proxy script
├── test_aiven.php              # Aiven MySQL CLI connection testing tool
├── db_setup.php                # 1-Click Visual Database Migrator & Health Check
├── README.md                   # Documentation, Credentials & Setup Guide
├── admin/                      # Admin Panel Controllers, Views & Modals
├── agent/                      # Real Estate Agent Portal Controllers & Views
├── classes/                    # OOP Core Logic (DBConnection, Login, SystemSettings, Users, Master)
├── database/                   # Original MySQL Schema (reps_db.sql)
├── dist/                       # AdminLTE 3 & Custom Styling / Scripts
├── plugins/                    # Bootstrap 4, jQuery, DataTables, FontAwesome
├── uploads/                    # Application Media & Uploads
├── config.php                  # Global App Configuration & Helper Functions
├── initialize.php              # Environment Variables & Dynamic URL Resolution
└── index.php                   # Frontend Public Portal
```
