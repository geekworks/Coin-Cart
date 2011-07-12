<?php
  /*********************************************************
  *  MyBitCoin SCI Automation Client Library for PHP v1.2  *
  * Copyright (c) 2010 MYBITCOIN LLC - ALL RIGHTS RESERVED *
  **********************************************************/

  require_once "sci-config.php";

  //Probably don't need to change anything below this line
  $mbc_version="v1.2";
  $default_return=array("SCI Reason" => "Error", "SCI Code" => "-1");

  function mbc_curl($file,$postdata) {
    global $mbc_version,$mbc_username,$mbc_sci_auto_key,$tor_enable,$tor_ip,$tor_port,$i2p_enable,$i2p_ip,$i2p_port;
    $postdata.="&username=".$mbc_username."&sci_auto_key=".$mbc_sci_auto_key;
    //figure out how we are connecting
    if($tor_enable==1) { //use tor
      $proxy=$tor_ip;
      $proxy_port=$tor_port;
      $ctimeout=120; //increase timeouts
      $timeout=240; 
      $hostname="http://xqzfakpeuvrobvpj.onion"; //don't change this
    } else if($i2p_enable==1) { //use i2p
      $proxy=$i2p_ip;
      $proxy_port=$i2p_port;
      $ctimeout=120; //increase timeouts
      $timeout=240;
      $hostname="http://nwpqc65o333ifqq7wqovo2ito5y3ca6rfygz3p6pusau26t6tpca.b32.i2p"; //don't change this
    } else { //direct over SSL (default)
      $ctimeout=$timeout=30;
      $proxy_port=$proxy="";
      $hostname="https://www.mybitcoin.com"; //don't change this
    }
    //do the post
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$hostname."/sci/".$file.".php");
    if($proxy!="") {
      curl_setopt($ch,CURLOPT_PROXY,$proxy);
      curl_setopt($ch,CURLOPT_PROXYPORT,$proxy_port);
    } else {
      curl_setopt($ch,CURLOPT_PORT,443);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0); //best to add our cert to curl and remove this line
    }
    curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$ctimeout);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_USERAGENT,"MBC SCI Client for PHP/".$mbc_version); //this gets eaten when running over I2P
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$postdata);
    $result=curl_exec($ch);
    curl_close($ch);
    return $result;
  }

  function mbc_gpg_verify($text) {
    if($text=="") return false;
    global $gpg_keypath,$gpg_binary,$tmp_path;
    putenv("GNUPGHOME=".$gpg_keypath);
    $tmp_file=tempnam($tmp_path,"msci");
    $fp=fopen($tmp_file,"w");
    if($fp) {
      fwrite($fp,$text);
      fclose($fp);
    } else return false;
    $tmp_file2=tempnam($tmp_path,"msci");
    system($gpg_binary." --verify ".$tmp_file." 1>".$tmp_file2." 2>".$tmp_file2);
    unlink($tmp_file);
    $fp=fopen($tmp_file2,"r");
    if($fp) {
      $buf=fread($fp,filesize($tmp_file2));
      fclose($fp);
    } else return false;
    unlink($tmp_file2);
    if(strpos($buf,"Good signature from \"MyBitcoin LLC (SCI Verification Key) <nobody@mybitcoin.com>\"")>0) return true; //signature is authentic
    return false;
  }

  function mbc_parse_response($response) {
    global $default_return;
    $return=$default_return;
    if($response=="") return $return;
    $split=preg_split("/\n\n/",$response); //remove the PGP clearsign stuff
    $response=preg_split("/\n/",$split[1]); //split by line
    $x=0;
    while($response[$x]!="") {
      $var=preg_split("/\:\ /",$response[$x]);
      $return[$var[0]]=trim($var[1],"\" ");
      $x++;
    }
    return $return;
  }

  function mbc_post_process($text) {
    if($text=="") {
      $return['SCI Reason']="Curl Failure!";
      return $return;
    }
    if(($gpg_enable==1)&&(mbc_gpg_verify($text)===false)) {
      $return['SCI Reason']="GPG Signature Failure!";
      return $return;
    }
    return mbc_parse_response($text); //parse and return array
  }

  //php function hooks
  function mbc_spend($bitcoin_addr,$amount,$note,$baggage) {
    return mbc_post_process(mbc_curl("auto-spend","amount=".$amount."&bitcoin_addr=".$bitcoin_addr."&note=".urlencode($note)."&baggage=".urlencode($baggage)));
  }

  function mbc_getbalance() {
    return mbc_post_process(mbc_curl("auto-getbalance",""));
  }

  function mbc_encryptformdata($form_data) {
    return mbc_post_process(mbc_curl("auto-encryptformdata","form_data=".urlencode($form_data)));
  }

  function mbc_getrates() {
    return mbc_post_process(mbc_curl("auto-getrates",""));
  }

php?>
