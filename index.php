<?php
	echo <<<_INFO
		<!DOCTYPE html>
		<html>
			<head>
			<meta charset='utf-8'>
			<title>クッキングサイト</title>
			<link rel='stylesheet' href='style.css'>
			<meta name="viewport" content="width=device-width, initial-scale=1.0" />
			</head>
			<body>
	_INFO;
	
	echo <<<_INIT
		<div id='index_header'>
		<h1>クッキングサイト</h1>
		<a href='homepage.php'>ホームページ</a>
		<div id='index_cat'>
			<a href='homepage.php?cat=デザート'><img src='images/sweets.png'>デザート<img src='images/sweets.png'></a>
			<a href='homepage.php?cat=ランチ'><img src='images/lunch.png'>ランチ<img src='images/lunch.png'></a>
			<a href='homepage.php?cat=ドリンク'><img src='images/drink.png'>飲み物<img src='images/drink.png'></a>
		</div>
		<a href='ingredient_search.php'>材料で探す</a>
	_INIT;
?>
</body></html>