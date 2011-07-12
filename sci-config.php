<?php

	//These should match your SCI Credentials (found on the "Merchant Tools" menu)
	$mbc_username = "coincart";
	$mbc_sci_auto_key = "apikey";

	//Be sure to also install our SCI public PGP key
	$gpg_binary = "/usr/bin/gpg";
	$gpg_keypath = "/home/username/.gnupg"; //file privledges are important
	$gpg_enable = 0; //1 = Enable GnuPG support (recommended for enhanced security), 0 = Disable

	//Proxy settings (Default = TOR/I2P is OFF. Connections are direct over SSL.)
	$tor_enable = 0; //1 = Enabled
	$tor_ip = "127.0.0.1"; //The IP address of your Tor server
	$tor_port = "8118"; //The port to your Privoxy/Polipo (usually 8118)

	$i2p_enable = 0; //1 = Enabled
	$i2p_ip = "127.0.0.1"; //The IP address of your I2P server
	$i2p_port = "4444"; //The port to your I2P HTTP proxy (usually 4444)

	//Temp path (The default is probably OK for most installations.)
	$tmp_path = "../coincart_sci_logs";

?>
