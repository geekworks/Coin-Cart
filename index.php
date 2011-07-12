<?php

	require_once "config.php";

	$c = db_connect();

	// fetch niches and products

		$niches = array(); $prices = array(); $products = array();

		$q = "SELECT product.id AS id, product.name AS product, product.price AS price, niche.name AS niche FROM product, niche WHERE product.active = 1 AND niche.id = product.niche";
		$r = mysql_query($q, $c);

		while($a = mysql_fetch_assoc($r)) {

			$niches[] = $a['niche'];

			if(!$prices[$a['niche']]) { $prices[$a['niche']] = array(); }
			if(!$products[$a['niche']]) { $products[$a['niche']] = array(); }

			$prices[$a['niche']][$a['id']] = $a['price'];
			$products[$a['niche']][$a['id']] = $a['product'];

		}

		$niches = array_unique($niches); sort($niches);

		# header('content-type: text/plain');
		# print_r($niches); print_r($prices); print_r($products); exit;

?><!doctype html>
<head> 

	<title>Bitcoin Bodega - Your Online Corner Store</title> 
	<meta name="description" content="Browse and purchase cheap accessories, clothing, and necessities." /> 
	<meta charset="utf-8" /> 
	<link href="inc/stylesheet.css" rel="stylesheet" type="text/css" /> 
	<link href="inc/facebox.css" rel="stylesheet" type="text/css" />

</head> 
<body> 

<div id="pageWrapper">

	<div id="pageHeader"><img src="inc/img/header.png" alt="Bitcoin Bodega" /></div>

	<div id="pageContent">

		<div id="pageNavigation">

<?php

	foreach($niches as $niche) {

		$line_ending = ($niche == end($niches)) ? "" : " &bull;";
		$niche_lower = strtolower($niche);

		echo <<<EOF
			<a href="#{$niche_lower}">{$niche}</a>{$line_ending}

EOF;

	}

?>

		</div>

		<div id="pageStore">

	<div style="font-size: 9pt; margin: 0 25px">
		First time here? If you're interested in purchasing an item, just click the <img src="inc/img/icon-purchase.png" alt="Purchase" style="height: 12px; position: relative; top: 1px" /> button. Shipping is cost is pre-calculated for the continental United States.
	</div>
<?php

	foreach($niches as $niche) {

		$niche_lower = strtolower($niche);

		echo <<<EOF
			<div class="storeCategory"><a name="{$niche_lower}"></a>{$niche}</div>

			<table border="0" cellpadding="0" cellspacing="0" width="100%">

EOF;

		$i = 1;

		foreach($products[$niche] as $product_id => $product_name) {

			$path  = "inc/img/products/item-{$product_id}.png";
			$image = file_exists($path) ? $path : "inc/img/icon-nopicture.png";
			$price = rtrim($prices[$niche][$product_id], "0"); if(substr($price, -1) == '.') { $price = $price . "0"; }

			if($i % 3 == 1) {
				echo <<<EOF
			<tr>

EOF;
			}

			if($i % 3 == 2) { $width = "34%"; } else { $width = "33%"; }

			echo <<<EOF
				<td align="center" width="{$width}">
					<img src="{$image}" alt="{$product_name}" />
					<div style="font-size: 10pt; font-weight: bold">{$product_name}</div>
					{$price} BTC <a href="purchase.php?id={$product_id}" rel="facebox"><img src="inc/img/icon-purchase.png" alt="Purchase Now!" style="display: inline; height: 24px; position: relative; left: 5px; top: 5px" /></a>
				</td>

EOF;

			if($i % 3 == 0) {
				echo <<<EOF
			</tr>

EOF;
			}

			if($i !== count($products[$niche])) { $i++; }

		}

		if($i % 3 !== 0) {

			while($i % 3 !== 0) {
				if($i % 3 == 2) { $width = "34%"; } else { $width = "33%"; }
				echo <<<EOF
				<td width="{$width}"></td>

EOF;
				$i++;
			}

			echo <<<EOF
			</tr>

EOF;

		}

		echo <<<EOF
			</table>

EOF;

	}

?>

		</div>

	</div>

	<div id="pageFooter">

		<a href="http://bitcoinbodega.com" title="Bitcoin Bodega">Bitcoin Bodega</a> is powered by <strong>Coin Cart v0.01a</strong>

	</div>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.5.1.min.js">\x3C/script>')</script>
<script src="inc/facebox.js"></script> 

<script type="text/javascript">
	$(document).ready(function() {
		$('a[rel*=facebox]').facebox({
			loadingImage : 'inc/img/loading.gif',
			closeImage   : 'inc/img/closelabel.png'
		})
	})
</script>

<script type="text/javascript">

	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-24236186-1']);
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();

</script>

</body>
</html>