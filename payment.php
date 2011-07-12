<?php

	$required = array('wallet', 'address', 'city', 'state', 'zip');
	foreach($required as $item) {
		if(!isset($_POST[$item]) OR empty($_POST[$item])) {
			header('content-type: text/plain');
			echo "There seems to be some sort of force field."; exit;
		}
	}

	require_once "sci-client.php";

	$address = $_POST['address'] . ", " .
	           $_POST['city'] . ", " .
	           $_POST['state'] . " " .
	           $_POST['zip'];

	$qs = array(
		'amount=' . urlencode($_POST['price']),
		'currency=BTC',
		'payee_bcaddr=1Dsfcc4fqW9MKT3chUf69S2fW5iAAYT9fB',
		'payee_name=' . urlencode('Coin Cart'),
		'note=' . urlencode($_POST['product']),
		'success_url=' . urlencode('http://bitcoinbodega.com'),
		'cancel_url=' . urlencode('http://bitcoinbodega.com'),
		'baggage=' . urlencode($_SERVER['REMOTE_ADDR'] . "|" . urlencode($_POST['wallet']) . "|" . $address)
	);

	$qs = implode('&', $qs);

	header("Location: https://www.mybitcoin.com/sci/paypage.php?{$qs}"); exit;

?>