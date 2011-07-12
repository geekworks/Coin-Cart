<?php

	// Coin Cart Settings

		$receipts = 'your@email.com';

		$database = 'coincart';
		$hostname = 'localhost';
		$username = 'coincart';
		$password = 'password';

	// Common Functions

		function db_connect() {
			global $hostname, $username, $password, $database;
			$conn = mysql_connect($hostname, $username, $password) or die(mysql_error());
			mysql_select_db($database, $conn);
			return $conn;
		}

		function cc_notify($recipient, $subject, $message) {
			$header = 'Content-type: text/plain; charset=UTF-8' . "\r\n" . 
			          'From: Coin Cart <no-reply@' . $_SERVER['HTTP_HOST'] . '>' . "\r\n" .
			          'Reply-To: no-reply@' . $_SERVER['HTTP_HOST'] . '>' . "\r\n" .
			          'X-Mailer: PHP/' . phpversion();
			return mail($recipient, $subject, wordwrap($message, 70), $headers);
		}

?>