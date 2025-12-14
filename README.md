# GDSS Retail - Decision Support System

A Group Decision Support System (GDSS) for selecting retail store supervisors using **Borda Count** (for group aggregation) and **TOPSIS** (for individual preference) methods. Built with Laravel 12.

## Prerequisites

Ensure you have the following installed on your local machine:
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL or MariaDB

## Installation Guide

Follow these steps to set up the project locally:

### 1. Setup Dependencies
Install the required PHP and JavaScript packages.

```bash
# Install PHP dependencies
composer install

# Install Frontend dependencies and build assets
npm install
npm run build
```

### 2. Environment Configuration
Copy the example environment file and generate the application key.

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup
The application requires a MySQL database.

1. **Create a Database** named `gdss_retail_db`.
2. **Import SQL Dump**: Import your provided SQL dump file into this database.
3. **Configure Credentials**: Open the `.env` file and update the database configuration. The application is configured to expect a user with root privileges and the following specific password:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gdss_retail_db
DB_USERNAME=root
DB_PASSWORD=gdss_retail_db_passwor_gdss_retail_db_pssword_gdss_retail_db_pasword
```

> **Note:** Ensure your local MySQL `root` user actually uses this password, or update the `.env` file to match your actual local database credentials. If you need to set up a specific user with this password, you can run the following SQL command:
> ```sql
> ALTER USER 'root'@'localhost' IDENTIFIED BY 'gdss_retail_db_passwor_gdss_retail_db_pssword_gdss_retail_db_pasword';
> FLUSH PRIVILEGES;
> ```

### 4. Run the Application
Start the local development server.

```bash
php artisan serve
```

The application will be accessible at `http://localhost:8000`.

## Features
- **Multi-Role Dashboard**: Admin, Area Manager, Store Manager, HR.
- **Candidate Management**: Comprehensive CV details (Portfolio, Domicile, etc.).
- **Real-time Access Logs**: Cyberpunk-styled login monitoring.
- **Consensus Calculation**: Automated Borda Count & TOPSIS processing.
- **Visual Analytics**: Interactive Bar Charts and Radar (Boundary) Charts.
- **Reporting**: PDF generation for decision results.

---
© 2025 GDSS Retail Project
