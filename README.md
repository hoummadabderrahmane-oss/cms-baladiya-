# CMS Baladiya 🏛️

A Municipality Management System built with PHP, MySQL, and Bootstrap 5.

## Features

- **Authentication** — Secure login/logout with password hashing
- **Dashboard** — Overview with statistics and recent activities
- **Citizens Management** — Full CRUD with search, filter, and pagination
- **Documents** — Upload and manage citizen documents (PDF, images)
- **Requests** — Submit and approve/reject citizen requests
- **Reports** — Analytics and statistics with CSV export
- **User Management** — Role-based access control (Admin/Staff)
- **Responsive Design** — Dark theme, mobile-friendly

## Installation

1. Clone or extract files to your web server directory
2. Create a MySQL database named `cms_baladiya`
3. Import `database/schema.sql`
4. Update `config/database.php` with your database credentials
5. Access via browser: `http://localhost/cms-baladiya/`

## Default Login

- **Username:** `admin`
- **Password:** `admin123`

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx with mod_rewrite

## File Structure
cms-baladiya/
├── assets/         # CSS, JS, uploads
├── auth/           # Login/logout
├── citizens/       # Citizen CRUD
├── config/         # Database config
├── dashboard/      # Dashboard
├── database/       # SQL schema
├── documents/      # Document management
├── includes/       # Shared components
├── reports/        # Analytics
├── requests/       # Request handling
├── users/          # User management
└── index.php       # Entry point