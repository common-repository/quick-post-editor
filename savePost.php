<?php

if(!defined("WP_PLUGIN_URL"))
	require_once(realpath("../../../")."/wp-config.php");
	
$postID=$_GET["post_id"];
$postTitle=$_GET["post_title"];
$postContent=$_GET["post_content"];

$postID=$_GET["post_id"];
$postTitle=$_GET["post_title"];
$postContent=$_GET["post_content"];

$newPost=array();
$newPost["ID"]=$postID;
$newPost["post_title"]=$postTitle;
$newPost["post_content"]=$postContent;

wp_update_post($newPost);

?>