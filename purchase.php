<?php

	if(!isset($_GET['id']) OR !is_numeric($_GET['id'])) {

		exit;

	}

	require_once "config.php";

	$c = db_connect();

	$q = "SELECT * FROM product WHERE id = {$_GET['id']}";
	$r = mysql_query($q, $c);
	$a = mysql_fetch_assoc($r);

?>
<div style="width: 500px">
	<div style="font-size: 9pt; padding: 10px">
		Please enter your shipping information below, then press the <img src="inc/img/icon-purchase.png" alt="Purchase" style="height: 12px; position: relative; top: 1px" /> button to proceed with the payment process. Your package will be addressed to the wallet address you provide in case of the need for a transaction refund.
	</div>
	<form action="payment.php" method="post">
		<input type="hidden" name="price" value="<?php echo $a['price']; ?>" />
		<input type="hidden" name="product" value="<?php echo $_GET['id']; ?>" />
		<input type="image" src="inc/img/icon-purchase-large.png" value="Purchase" alt="Purchase" style="float: right; margin: 35px 35px 0 0" />
		<table border="0" cellspacing="0" cellpadding="10" border="0" width="300">
		<tr>
			<td colspan="3" width="100%">
				Refund Wallet Address:<br />
				<input type="text" style="width: 100%" name="wallet" />
			</td>
		</tr>
		<tr>
			<td colspan="3" width="100%">
				Street Address:<br />
				<input type="text" style="width: 100%" name="address" />
			</td>
		</tr>
		<tr>
			<td width="50%">
				City:<br />
				<input type="text" style="width: 100%" name="city" />
			</td>
			<td width="20%">
				State<br />
				<input type="text" style="width: 100%" name="state" />
			</td>
			<td width="30%">
				ZIP<br />
				<input type="text" style="width: 100%" name="zip" />
			</td>
		</tr>
		</table>
	</form>
</div>