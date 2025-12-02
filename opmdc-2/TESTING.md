Local testing steps for OPMDC proposal flow

1) Prepare the database
   - Import the SQL schema: `opmdc_db.sql` (this creates DB `opmdc` and adds example users and the `project_proposals` table).
     Example (MySQL CLI):

     mysql -u root -p < opmdc_db.sql

   - Ensure `opmdc-2/db.php` has `$dbName = 'opmdc';` (it was updated already).

2) Start XAMPP/Apache/PHP and visit the app in a browser.

3) Login as a staff or head user
   - Use `/opmdc-2/login.html` and sign in with credentials from the SQL:
     - staff / password (role: OPMDC Staff)
     - admin / password (role: OPMDC Head)

4) Submit a proposal as a Barangay Official
   - Login as a barangay user (e.g., brgy1 / password) and open `barangay-dashboard.php`.
   - Click New Proposal and submit the form (you may include an attachment).
   - Confirm the proposal appears locally in the dashboard and in the DB `project_proposals` table.

5) Review and change status as staff/head
   - Login as `staff` and open `staff-dashboard.php`.
   - Click View on a pending proposal, use Approve/Decline in the modal.
   - Verify status updated in DB and that history JSON contains an entry with the staff user's name.

6) Useful curl examples (PowerShell)
   - Login (this will set session cookie - use a browser or a tool that preserves cookies; for quick tests use the web UI):
     curl -Method POST -Uri "http://localhost/mycapstone/opmdc-2/authenticate.php" -Body @{ credential='staff'; password='password' } -SessionVariable session

   - List proposals:
     curl "http://localhost/mycapstone/opmdc-2/api/list_staff_proposals.php"

   - Update proposal status (needs session cookie — easier from the web UI):
     curl -Method POST -Uri "http://localhost/mycapstone/opmdc-2/api/update_proposal.php" -Body @{ id=12; status='Approved'; note='Okay' } -WebSession $session

7) Notes and caveats
   - PHP CLI (`php -l`) may not be installed on your system by default; test endpoints via browser/XAMPP.
   - For security: consider adding CSRF protection and more robust file validation for attachments.

If you want, I can:
- Add inline note input to the staff modal (remove the prompt),
- Harden file upload validation (MIME type & size),
- Add server-side role-based redirect for other pages.
