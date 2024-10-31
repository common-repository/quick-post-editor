<?php

error_reporting(0);

if(!defined("WP_PLUGIN_URL"))
	require_once(realpath("../../../")."/wp-config.php");

$id=$_GET["post_id"];	

$post=get_post($id);	

$returnString=$post->post_title."|".$post->post_content;

echo $returnString;

?>