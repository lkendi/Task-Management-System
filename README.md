# Task Management System

A modern task and project management system built with **Laravel 12**, **Vue 3**, and **Inertia.js**.

## Features
- Role-based access: Admin and regular user dashboards
- Project & Task CRUD
- User management (admin only)
- Email notifications for task assignments
- Modern UI with Tailwind CSS
- Backend feature tests

## Getting Started

1. **Clone the repository**
   ```bash
   git clone https://github.com/lkendi/Task-Management-System
   cd task-management-system
   ```
2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```
3. **Set up environment**
   - Copy `.env.example` to `.env` and update your database credentials
   - Generate app key:
     ```bash
     php artisan key:generate
     ```
4. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
5. **Start development servers**
   ```bash
   npm run dev
   php artisan serve
   ```
   Visit [http://localhost:8000](http://localhost:8000)

   Admin Credentials:
   email: admin@example.com
   password: password

   Feel free to create a regular user