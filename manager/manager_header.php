<?php
	require_once("login_info.php");
	/*
	*	MANAGER LOGIN 
	*/
	if(!isset($_SERVER['PHP_AUTH_USER'])){
		header('WWW-Authenticate: Basic realm="My Realm"');
		header('HTTP/1.0 401 Unauthorized');
		die();
	}
	else if($_SERVER['PHP_AUTH_USER'] != $root_user || $_SERVER['PHP_AUTH_PW'] != $root_pass){
		die();
	}

	require_once("../database/db_mappers.php");
	define("IMAGE_PATH", "../recipe_images/");
	
	// info html
	echo <<<_INIT
	<!DOCTYPE html>
	<html>
	<head>
		<meta chartset='utf-8'>
		<link rel='stylesheet' href='manager.css'>
	</head>
	<body>
	_INIT;
	// header
	echo <<<_HEAD
	<div id='adm-header'>
		<a href='recipe_manager.php'><h4>HOME</h4></a>
		<a href='add_recipe.php'><h4>ADD RECIPE</h4></a>
		<form method='GET'>
			<input type='text' name='q'>
			<input type='submit' value='search'>
		</form>
	</div>
	_HEAD;
	
		
	/*
	*	This function handles image uploading
	*	And renames them according to its own recipe
	*/
	function GetCreateImage(string $form_img, string $image_name){
		if($_FILES){
			// strstr() in order to save the file extension
			$sent_file = IMAGE_PATH . $image_name . strstr($_FILES[$form_img]['name'], '.');
			move_uploaded_file($_FILES[$form_img]['tmp_name'], $sent_file);
			return $sent_file;
		}
		else
			throw new Exception("No image indexed");
	}
	/*
	*	Navigation Arrows function (to navigate through recipes on
	*	recipe_manager.php)
	*/
	function GetNavArrows(DataMapper $mapper, bool $left = true, bool $right = true) : string{
		$frontOff = $mapper->GetOffset() + $mapper->GetLimit();
		$backOff = ($mapper->GetOffset() - $mapper->GetLimit()) > 0 ? $mapper->GetOffset() - $mapper->GetLimit() : 0;
		
		$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$actual_link = preg_replace('/[&?]of=[0-9]+/', '', $actual_link);
		unset($_GET['of']);
		$met = sizeof($_GET) > 0 ? '&' : '?'; 
		$frontLink = $actual_link . $met . 'of=' . $frontOff;
		$backLink = $actual_link . $met . 'of=' . $backOff;
		
		$text = "<div>";
		if($left)
			$text .= "<a href='$backLink' style='margin: 10px'><button>previous page</button></a>";
		if($right)
			$text .= "<a href='$frontLink' style='margin: 10px'><button>next page</button></a>";

		$text .= '</div>';
		return $text;
	}
?>