# stock-app
# 📌 Stock Price Upload & Analysis (Laravel 12 + Livewire + Queue + Spout)

This project allows uploading **Excel/CSV stock price files** for different companies.  
Data is processed in the **background queue** and stored in the database.  
You can query stock performance using REST APIs (`compare` and `period` endpoints).

---

## 🚀 Features
- Upload Excel/CSV with **Livewire 3**.
- Process files in **queue jobs** (async, non-blocking).
- Uses **Box/Spout** for memory-efficient Excel parsing.
- Companies → Stock Prices (**one-to-many relation**).
- APIs for:
  - Compare stock price between two dates.
  - Get stock performance by period (`1M`, `1Y`, `MAX`, etc).
- Postman collection included.

---

## ⚙️ Requirements
- PHP 8.2+
- Composer 2+
- MySQL or SQLite
- Node.js & NPM (for frontend/Livewire dev)
- Laravel 12.x

---

## 📦 Installation

```bash
# 1. Clone repo
git clone https://github.com/JonMoxely/stock-app.git
cd stock-app

# 2. Install PHP dependencies
composer install

# 3. Install frontend dependencies (if required)
npm install && npm run build

# 4. Copy .env
cp .env.example .env
```

---

## 🔑 Environment Setup

Edit `.env` and configure your DB:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stocks
DB_USERNAME=root
DB_PASSWORD=
```

Also enable queue:

```env
QUEUE_CONNECTION=database
```

---

## 🗄️ Database

Run migrations:

```bash
php artisan migrate
```

                  
## 📂 Storage Link

Create symbolic link for file storage:

```bash
php artisan storage:link
```

---

## ▶️ Running the Project

Start Laravel server:

```bash
php artisan serve
```

Start queue worker (for background jobs):

```bash
php artisan queue:listen
# OR
php artisan queue:work
```

Now you can upload Excel files via Livewire form (`/upload-stock`) and they’ll be processed in background.

---

## 📡 API Endpoints

Base URL: `http://127.0.0.1:8000`

### 1. Compare Between Dates
```
GET /api/stocks/compare?company=ABC&start=2023-01-01&end=2023-06-01
```

Response:
```json
{
  "company_id": 1, 
  "company_name": "ABC",
  "data": {
    "start_date": "2023-01-01",
    "end_date": "2023-06-01",
    "start_price": 100.0,
    "end_price": 120.0,
    "percentage_change": 20.0
  }
}
```

### 2. Get By Period
```
GET /api/stocks/period?company=ABC&period=1Y
```

Response:
```json
{
  "company_id": 1,
  "company_name": "ABC",
  "period": "1Y",
  "data": {
    "period": "1Y",
    "start_date": "2024-01-01",
    "end_date": "2025-01-01",
    "start_price": 90.0,
    "end_price": 150.0,
    "percentage_change": 66.67
  }
}
```

---

## 🧪 Postman Collection

A Postman collection is included in `/postman/postman_collection.json`.

To use:
1. Open Postman → Import.
2. Select the JSON file.
3. Run requests against `http://127.0.0.1:8000`.

---

## 🛠️ Useful Commands

```bash
# Run migrations fresh
php artisan migrate:fresh

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run queue worker
php artisan queue:work
```

---

## 📚 Tech Stack

- Laravel 12
- Livewire 3
- MySQL
- Box/Spout (Excel/CSV reader)
- Queue (database driver)
- Postman (API testing)

---
