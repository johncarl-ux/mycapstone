# OPMDC-2 - Database & Login Setup

This folder contains a simple MySQL schema and minimal PHP endpoints to enable server-side login for the existing `login.html` UI.

Files added
- `opmdc_db.sql` - SQL schema for an `opmdc` database with a `users` table and example accounts.
- `db.php` - mysqli connection helper (edit credentials if needed).
- `authenticate.php` - login endpoint; accepts POST `credential` (username or email) and `password`.

Quick setup

1. Import the database using the MySQL CLI (from Windows PowerShell):

   mysql -u root -p < "C:\xampp\htdocs\mycapstone\opmdc-2\opmdc_db.sql"

   Or use phpMyAdmin to import `opmdc_db.sql`.

2. Confirm `db.php` credentials match your MySQL setup. By default it uses host `127.0.0.1`, user `root`, empty password, database `opmdc`.

3. The example accounts in `opmdc_db.sql` use the plaintext password: `password` (hashed in the SQL). Change them after import.

Creating/updating a password securely

If you want to create a new user or update a password, generate a bcrypt hash using PHP. Example (run with XAMPP PHP):

   & "C:\xampp\php\php.exe" -r "echo password_hash('yourplaintext', PASSWORD_DEFAULT) . PHP_EOL;"

Then use the generated hash in an INSERT or UPDATE statement for the `password` column.

How the login works

- The UI in `login.html` POSTs to `authenticate.php` using fetch and expects a JSON response.
- `authenticate.php` looks up the user by username or email, checks `status` (must be `approved` or `active`) and verifies the password with `password_verify`.
- On success it sets `$_SESSION['user']` and returns `{ success: true, role: '...' }` so the client can redirect to the appropriate dashboard.

Testing

- Open `http://localhost/mycapstone/opmdc-2/login.html` in your browser.
- Log in with one of the example accounts (username/email: `admin`/`admin@example.com`, password: `password`).

Security notes

- This is a minimal example. For production, use HTTPS, stronger session configuration, CSRF tokens, rate limiting, and a proper user management UI.
