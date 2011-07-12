<?php

	require_once "sci-client.php";

	if((isset($_POST['input']))&&(isset($_POST['sci_auto_key']))&&($_POST['input']!="")&&($_POST['sci_auto_key']!="")) {

		if($_POST['sci_auto_key']!=$mbc_sci_auto_key) { //verify the sci key
		  echo "ERROR\n";
		  exit;
		}
		$response=mbc_post_process($_POST['input']); //Returns an array of pgp verified transaction data.

    //*** Do something here such as connect to your database and save the transaction data, etc.

	if(is_numeric(urldecode($response['SCI Payment Note']))) {

		// Prepare payment for processing

			$parts = explode("|", urldecode($response['SCI Baggage Field']));
			$order = array(
				'ip' => $parts[0],
				'product' => urldecode($response['SCI Payment Note']),
				'refund_to' => $parts[1],
				'ship_to' => $parts[2],
				'transaction_number' => $response['SCI Transaction Number'],
				'transaction_time' => $response['SCI Transaction Date']
			);

		// Process payment to shopping cart

			require_once 'cc-config.php';

			$c = db_connect();

			$q = "SELECT * FROM btcbodega.product WHERE id = {$order['product']}";
			$r = mysql_query($q, $c);
			$a = mysql_fetch_assoc($r);

			if($response['SCI Amount'] == $a['price']) {

				$q = "INSERT INTO btcbodega.order (ip, product, refund_to, ship_to, transaction_number, transaction_time) VALUES ('{$order['ip']}', {$order['product']}, '{$order['refund_to']}', '{$order['ship_to']}', {$order['transaction_number']}, '{$order['transaction_time']}')"; 
				mysql_query($q, $c);

				$q = "UPDATE btcbodega.product SET product.active = 0 WHERE product.id = {$order['product']}";
				mysql_query($q, $c);

				cc_notify($receipts, "Coin Cart Purchase: {$a['name']}", "You have received a payment of {$response['SCI Amount']} from {$order['refund_to']}; this payment was for the product '{$a['name']}' and needs to be shipped to the address below:\n\n{$order['ship_to']}");

			} else {

				echo "ERROR\n"; exit;

			}

	} else {

		 echo "ERROR\n"; exit;

	}



		//Our example here just logs the incoming data to a file in your temp directory.
		$output="";
		foreach($response as $key => $value) {
		  $output.=$key." = ".$value."\n";
		}
		$fp=fopen($tmp_path."/sci-receipt-handler.log","a");
		if($fp) {
		  if(flock($fp,LOCK_EX)) {
			fwrite($fp,$output."\n\n");
			// fwrite($fp,$q."\n\n\n\n");
			flock($fp,LOCK_UN);
		  }
		  fclose($fp);
		}
		//end of logging example

	} else echo "ERROR\n";

?>
