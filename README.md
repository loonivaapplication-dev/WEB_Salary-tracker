# WEB_Salary-tracker
PHP and MySQL salary tracker. This branch contains student registration, login, logout, and profile management.

1. Start Apache and MySQL in the XAMPP Control Panel.
2. Import `users.sql` in phpMyAdmin. When combining modules, import it before the work-record schema so the `Users` table exists.
3. Open [registration](http://localhost/WEB_Salary-tracker/register.php) or [sign in](http://localhost/WEB_Salary-tracker/login.php).

The connection defaults to XAMPP's local MySQL settings. Set `SALARY_DB_DSN`, `SALARY_DB_USER`, and `SALARY_DB_PASSWORD` in the PHP environment if your database uses different settings. After login, other modules can read the account ID from `$_SESSION['user_id']`.
