<?php
// Logout: destroy server session and instruct the client to clear localStorage, then redirect to login
session_start();
$_SESSION = [];
session_destroy();
// Expire session cookie so browser stops sending the old session id
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000, $params['path'] ?? '/', $params['domain'] ?? '', $params['secure'] ?? false, $params['httponly'] ?? true);
}
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="0">
	<title>Logging out…</title>
	<script>
		(function(){
			try {
				localStorage.removeItem('loggedInUser');
				localStorage.removeItem('opmdcNewSubmissions_Staff');
				localStorage.removeItem('opmdcNewSubmissions_Head');
				localStorage.removeItem('opmdcDashboardCounts');
				if (window.sessionStorage) {
					sessionStorage.clear();
				}
			} catch (e) {}
			// Small delay to allow storage ops, then navigate
			setTimeout(function(){ window.location.replace('login.html'); }, 50);
		})();
	</script>
</head>
<body>Logging out…</body>
</html>
