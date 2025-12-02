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

Notifications & SSE

This project includes a small notification system used by the dashboards (barangay, staff, head). New files/endpoints added:

- `notifications.php` — CRUD endpoint (creates table if needed) for notifications.
- `notifications_list.php` — returns recent notifications plus `unread_count` for the logged-in user's role/user_id. Accepts `limit` and `since_id`.
- `notifications_stream.php` — Server-Sent Events (SSE) endpoint that streams newly inserted notifications to authenticated clients. It filters by the PHP session `role` or `user_id`.
- `notifications_mark_read.php` — mark a notification as read.

How it works (quick)

1. Client posts a new notification to `notifications.php` (e.g., the Barangay dashboard posts when a proposal is submitted). The endpoint inserts into the DB and returns the inserted `id`.
2. `notifications_stream.php` polls for new rows and emits SSE `notification` events to connected, authenticated clients.
3. Dashboards open an EventSource to `notifications_stream.php` and refresh or append notifications when events arrive. They can also call `notifications_list.php` to fetch a recent list and unread count.

Testing realtime notifications

1. Ensure Apache/PHP is running and you have a logged-in session in the browser (login via `login.html`).
2. Open the target dashboard(s): `barangay-dashboard.php`, `staff-dashboard.php`, `head-dashboard.php`.
3. Submit a new proposal from the barangay dashboard. Check the Network tab for the POST to `notifications.php` (should return JSON with `id`).
4. Other dashboards (staff/head) should receive the notification without manual refresh; if not, check the browser console and Apache error logs.

Troubleshooting

- If SSE appears not to deliver: ensure any reverse-proxy or load balancer is not buffering the SSE response. SSE requires the server to stream events unbuffered.
- The SSE endpoint uses PHP sessions. Ensure EventSource connections include cookies (same-origin) — cross-origin setups require careful CORS and credentials handling.
- For heavier load, consider swapping the polling SSE for a Redis pub/sub or WebSocket server for scalability.

If you want, I can add a test script to POST a notification and show the returned DB id, or add a small admin UI for creating test notifications.
