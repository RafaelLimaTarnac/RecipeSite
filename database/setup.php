<?php
	// Run once in order to create tables
	require_once("dbase_utils.php");

	// preview_recipe
	$query = "create table if not exists preview_recipe(" .
	"id int unsigned not null auto_increment," .
	"name varchar(35) not null," . "description text not null," .
	"image_file varchar(100) not null," . "duration time not null," .
	"difficulty enum('簡単', '普通', '難しい')," . "category enum('デザート', 'ドリンク', 'ランチ'), " .
	"primary key (id)," .
	"index(name))";
	$pdo->query($query);
	
	// recipe
	$query = "create table if not exists recipe(".
	"id int unsigned not null," . "method text not null," .
	"image_file varchar(100) not null," .
	"foreign key(id) references preview_recipe(id) on delete cascade on update cascade)";
	$pdo->query($query);
	
	// ingredient
	$query = "create table if not exists ingredient(name varchar(25) not null," .
	"primary key(name))";
	$pdo->query($query);
	
	// ingredient_recipe
	$query = "create table if not exists ingredient_recipe(".
	"recipe_id int unsigned not null," . "quantity varchar(25) not null," .
	"ingredient_name varchar(25) not null," .
	"foreign key(recipe_id) references preview_recipe(id) on delete cascade on update cascade," .
	"foreign key(ingredient_name) references ingredient(name) on delete cascade on update cascade)";
	$pdo->query($query);
?>