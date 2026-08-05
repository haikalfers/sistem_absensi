# Sistem Absensi Karyawan

Sistem Absensi Karyawan (Employee Attendance System) is a comprehensive web-based application built with Laravel 12. It helps organizations manage employee attendance, leave requests, overtime, and payroll generation efficiently.

## 🚀 Features

- **Role-Based Access Control**: Secure authentication and authorization using Spatie Permissions (Admin and Employee roles).
- **Employee Dashboard**: Self-service portal for employees to clock in/out, view attendance history, and request leaves.
- **Admin Dashboard**: Comprehensive overview of daily attendance, leave approvals, and employee management.
- **Attendance Tracking**: Real-time tracking of employee check-ins and check-outs.
- **Leave Management**: Seamless process for employees to apply for leaves and admins to approve or reject them.
- **Overtime Management**: Track and manage employee overtime hours.
- **Payroll Integration**: Generate payroll based on attendance, overtime, and leave data.
- **Export & Import**: Export attendance, leave, and payroll reports to Excel and PDF formats.

## 🛠️ Tech Stack

- **Framework**: [Laravel 12](https://laravel.com/)
- **Frontend**: [Tailwind CSS](https://tailwindcss.com/) & [Alpine.js](https://alpinejs.dev/)
- **Authentication**: Laravel Breeze
- **Database**: MySQL / PostgreSQL
- **PDF Generation**: [Barryvdh DOMPDF](https://github.com/barryvdh/laravel-dompdf)
- **Excel Export/Import**: [Maatwebsite Excel](https://laravel-excel.com/)
- **Role Management**: [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6/introduction)
- **Image Processing**: [Intervention Image](https://image.intervention.io/)

## 📦 Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL or other supported database

## ⚙️ Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/haikalfers/sistem_absensi.git
   cd sistem_absensi
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install NPM dependencies**
   ```bash
   npm install
   ```

4. **Copy the `.env` file**
   ```bash
   cp .env.example .env
   ```

5. **Generate the application key**
   ```bash
   php artisan key:generate
   ```

6. **Configure the Database**
   Open the `.env` file and update your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sistem_absensi
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Run the database migrations and seeders** (if any)
   ```bash
   php artisan migrate --seed
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   ```

9. **Serve the application**
   ```bash
   php artisan serve
   ```
   
   The application will be available at `http://localhost:8000`.

## 🧑‍💻 Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
