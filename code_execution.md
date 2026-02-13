# Code Execution Guide

## Prerequisites
- **Web Server**: Apache (XAMPP/WAMP/Laragon) or Nginx.
- **PHP**: Version 8.1 or higher.
- **Database**: MySQL.
- **Composer**: For dependency management.
- **Browser**: Chrome, Firefox, or Edge.

## Setup Instructions

1.  **Database Setup**:
    -   Create a new MySQL database (e.g., `icefat_db`).
    -   Import the database schema if provided (check `database.sql` or similar if exists, otherwise migration might be needed). *Note: No SQL file was explicitly seen in the root, but Models imply a schema.*
    -   Configure `app/Config/Database.php` or `.env` with your database credentials.

2.  **Configuration**:
    -   Rename `env` to `.env`.
    -   Set `CI_ENVIRONMENT = development` for debugging.
    -   Set `app.baseURL` to your local URL (e.g., `http://localhost/ICEFAT/public/`).

3.  **Dependencies**:
    -   Run `composer install` in the project root to install PHP dependencies.

4.  **Permissions**:
    -   Ensure `writable/` folder is writable by the web server.

## Running the Application

### Option A: Using XAMPP/Local Web Server
1.  Place the project folder `ICEFAT` in `htdocs` (e.g., `E:\xampp\htdocs\ICEFAT`).
2.  Start Apache and MySQL modules in XAMPP Control Panel.
3.  Access the application in your browser: `http://localhost/ICEFAT/public/`

### Option B: Using CodeIgniter Spark Server
1.  Open a terminal in the project root.
2.  Run the command:
    ```bash
    php spark serve
    ```
3.  Access the application at: `http://localhost:8080`

## Login Credentials
Use the following credentials to log in:
-   **User**: `app4icefat`
-   **Password**: `5r8U(A{7wXXa`

## Troubleshooting
-   **404 Not Found**: Ensure `.htaccess` is configured correctly in `public/` or that `mod_rewrite` is enabled in Apache. CodeIgniter 4 public folder handling requires this.
-   **Database Errors**: Check `.env` database settings.
-   **Permission Errors**: Check read/write permissions for the `writable` directory.
