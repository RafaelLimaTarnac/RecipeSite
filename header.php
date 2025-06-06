<?php
	require_once("database/db_mappers.php");
	echo <<<_INIT
	<!DOCTYPE html>
	<html>
	<head>
			<meta charset='utf-8'>
			<title>Hatsue Cooking</title>
			<link rel='stylesheet' href='style.css'>
			<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	</head>
	<body>
		<div id='page_header'>
			<a href='homepage.php'>ホームページ</a>
			<a href='homepage.php?cat=デザート'><img src='images/sweets.png'>デザート<img src='images/sweets.png'></a>
			<a href='homepage.php?cat=ランチ'><img src='images/lunch.png'>ランチ<img src='images/lunch.png'></a>
			<a href='homepage.php?cat=ドリンク'><img src='images/drink.png'>飲み物<img src='images/drink.png'></a>
			<a href='ingredient_search.php'>材料で探す</a>
		</div>
	_INIT;
	/*
	*	GetNavArrows uses a BaseMapper, so we can use it in both ingredient and preview_recipe queries
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
		
		$text = "<div class='nav-arrows'>";
		if($left){
		$text .= <<<_TEST
			<a href='$backLink'>
				<img src='images/left_arrow.png'>
				<p>前のページ</p>
			</a>
			_TEST;
		}
		if($right){
		$text .= <<<_TEST
			<a href='$frontLink'>
				<p>次のページ</p>
				<img src='images/right_arrow.png'>
			</a>
			_TEST;
		}
		$text .= '</div>';
		return $text;
	}
?>