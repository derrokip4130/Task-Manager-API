# Task Manager API


---

## 🌐 Live API

Production Base URL:

```text
https://task-manager-452.up.railway.app/api/tasks
```

Quick test:

```bash
curl -X GET "https://task-manager-452.up.railway.app/api/tasks" \
  -H "Accept: application/json"
```

---

## 🛠 Tech Stack

- Laravel 12
- PHP
- MySQL
- Railway (Deployment)

> This project uses **MySQL** both locally and in production.

---

## 🚀 How to Run Locally

### 1) Clone the repository
```bash
git clone https://github.com/derrokip4130/Task-Manager-API
cd task_manager
```

### 2) Install dependencies
```bash
composer install
```

### 3) Set up environment variables
Copy the example environment file and generate the app key:

```bash
cp .env.example .env
php artisan key:generate
```

### 4) Configure MySQL in `.env`
Update your `.env` file with your MySQL credentials:

```env
APP_NAME=TaskManager
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=
```

### 5) Create the database
Create a MySQL database named `task_manager`.

Example in MySQL:

```sql
CREATE DATABASE task_manager;
```

### 6) Run migrations
```bash
php artisan migrate
```

### 7) Start the development server
```bash
php artisan serve
```

The app will be available at:

```text
http://127.0.0.1:8000
```

API base URL:

```text
http://127.0.0.1:8000/api/tasks
```

---

## ☁️ How to Deploy

You can deploy this Laravel API on **Railway**

---

## ☁️ Deploy on Railway

This project can be deployed online using **Railway** with a **MySQL database**.

### 1) Push the project to GitHub
Make sure your Laravel project is committed and pushed to a GitHub repository.

```bash
git add .
git commit -m "prepare app for deployment"
git push
```

---

### 2) Create a Railway project
- Go to **Railway**
- Click **New Project**
- Choose **Deploy from GitHub Repo**
- Select this repository

Railway will automatically create a service for the Laravel app.

---

### 3) Add a MySQL database
Inside the same Railway project:

- Click **+ New**
- Choose **Database**
- Select **MySQL**

Railway will create a MySQL service and automatically expose connection variables.

---

### 4) Add environment variables
Open the **Laravel app service** → **Variables** and add:

```env
APP_NAME=TaskManager
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_URL=https://task-manager-452.up.railway.app

LOG_CHANNEL=stderr
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
```

Generate the Laravel app key locally using:

```bash
php artisan key:generate --show
```

Copy the output and paste it into:

```env
APP_KEY=
```

---

### 5) Set the build and start commands

#### Build Command
In **Settings → Build Command**, set:

```bash
composer install --no-dev --optimize-autoloader
```

#### Start Command
In **Settings → Start Command**, set:

```bash
php artisan serve --host=0.0.0.0 --port=$PORT
```

---

### 6) Run database migrations
After deployment, open the Railway shell and run:

```bash
php artisan migrate
```

This will create the database tables and populate sample task data for testing.

---

### 7) Generate a public domain
In the Laravel service:

- Open **Networking**
- Click **Generate Domain**

Railway will provide a public URL such as:

```text
https://task-manager-452.up.railway.app
```

Your live API base URL will then be:

```text
https://task-manager-452.up.railway.app/api/tasks
```

---

### 8) Test the deployed API
Example:

```bash
curl -X GET "https://task-manager-452.up.railway.app/api/tasks" \
  -H "Accept: application/json"
```

Expected:
- `200 OK`
- JSON response containing tasks or a meaningful empty-state message

---

## 🔌 Example API Requests

Base URL (local):

```text
http://127.0.0.1:8000/api
```

Base URL (production):

```text
https://task-manager-452.up.railway.app/api
```

You can test the API using:
- cURL
- Postman
- Thunder Client
- Insomnia

---

## 1) Create Task

### Endpoint
```http
POST /api/tasks
```

### Example Request
```bash
curl -X POST "http://127.0.0.1:8000/api/tasks" -H "Accept: application/json" -H "Content-Type: application/json" -d '{"title":"Review deployment logs","due_date":"2026-04-11","priority":"high"}'
```

### Rules
- `title` cannot duplicate another task with the same `due_date`
- `priority` must be `low`, `medium`, or `high`
- `due_date` must be today or later

### Example Response
```json
{
    "title": "Test API route",
    "priority": "high",
    "due_date": "2026-04-09",
    "status": "pending",
    "updated_at": "2026-03-31T13:52:31.000000Z",
    "created_at": "2026-03-31T13:52:31.000000Z",
    "id": 10
}
```

---

## 2) List Tasks

### Endpoint
```http
GET /api/tasks
```

### Example Request
```bash
curl -X GET "http://127.0.0.1:8000/api/tasks" -H "Accept: application/json"
```

### Optional status filter
```bash
curl -X GET "http://127.0.0.1:8000/api/tasks?status=pending" -H "Accept: application/json"
```

### Rules
- Sorted by priority: `high → medium → low`
- Then sorted by `due_date` ascending
- Optional `status` query parameter
- Returns meaningful JSON if no tasks exist

### Example Response
```json
[
    {
        "id": 6,
        "title": "Test API route",
        "due_date": "2026-03-31",
        "priority": "high",
        "status": "pending",
        "created_at": "2026-03-31T12:49:45.000000Z",
        "updated_at": "2026-03-31T12:49:45.000000Z"
    },
    {
        "id": 7,
        "title": "Test API route",
        "due_date": "2026-04-03",
        "priority": "high",
        "status": "pending",
        "created_at": "2026-03-31T12:59:21.000000Z",
        "updated_at": "2026-03-31T12:59:21.000000Z"
    }
]
```

---

## 3) Update Task Status

### Endpoint
```http
PATCH /api/tasks/{id}/status
```

### Example Request
```bash
curl -X PATCH "http://127.0.0.1:8000/api/tasks/1/status" -H "Accept: application/json"
```

### Rules
- Status progression only:
  - `pending → in_progress → done`
- Cannot skip status
- Cannot revert status

### Example Response
```json
{
    "message": "Task status updated successfully",
    "data": {
        "id": 1,
        "title": "Test API route",
        "due_date": "2026-03-31",
        "priority": "high",
        "status": "done",
        "created_at": "2026-03-30T08:44:33.000000Z",
        "updated_at": "2026-03-31T07:41:08.000000Z"
    }
}
```

---

## 4) Delete Task

### Endpoint
```http
DELETE /api/tasks/{id}
```

### Example Request
```bash
curl -X DELETE "http://127.0.0.1:8000/api/tasks/1" -H "Accept: application/json"
```

### Rules
- Only tasks with status `done` can be deleted
- Attempts to delete `pending` or `in_progress` tasks return `403 Forbidden`

### Example Response
#### Deleting a task that is pending or in progress 
```json
{
    "message": "Only tasks with status done can be deleted"
}
```

#### Deleting a task that is done 
```json
{
    "message": "Task deleted successfully"
}
```
---

## 5) Daily Report

### Endpoint
```http
GET /api/tasks/report?date=YYYY-MM-DD
```

### Example Request
```bash
curl -X GET "http://127.0.0.1:8000/api/tasks/report?date=2026-03-28" -H "Accept: application/json"
```

### Example Response
```json
{
  "date": "2026-03-28",
  "summary": {
    "high": {
      "pending": 2,
      "in_progress": 1,
      "done": 0
    },
    "medium": {
      "pending": 1,
      "in_progress": 0,
      "done": 3
    },
    "low": {
      "pending": 0,
      "in_progress": 0,
      "done": 1
    }
  }
}
```