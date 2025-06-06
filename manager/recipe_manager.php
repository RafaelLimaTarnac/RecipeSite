<?php
	require_once("manager_header.php");
	
	$mapper = new PreviewMapper();
	$mapper->setLimit(10);
	if(isset($_GET['of']))
		$mapper->setOffset($_GET['of']);	
	
	if(isset($_POST['delete'])){
		// Deletes our images
		unlink($mapper->Select($_POST['id'])->img);
		$recip = new RecipeMapper();
		unlink($recip->Select($_POST['id'])->img);
		$recip = null;
		// Deletes recipe
		$mapper->Remove($_POST['id']);
		/*
		*	Deletes ingredients if they aren't listed
		*	in any recipes
		*/
		$ing = new IngredientMapper();
		$ing->Remove(null);
	}
	
	if(isset($_GET['q'])){
		$query['name'] = $_GET['q'];
		$recipes = $mapper->Select($query);
	}
	else
		$recipes = $mapper->Select();

	$limiter = 0;

	echo "<div id='recipe-prev-adm'>";
	foreach($recipes as $i){
		$limiter++;
		echo <<<_PREV
		<div class='recipe-prev-adm-ind'>
			<table class='info-table-adm'>
				<tr>
					<th><p>id</p></th>
					<th><p>name</p></</th>
					<th><p>duration</p></</th>
					<th><p>difficulty</p></</th>
					<th><p>category</p></</th>
				</tr>
				<tr>
					<td><p>$i->id</p></</td>
					<td><p>$i->name</p></</td>
					<td><p>$i->duration</p></</td>
					<td><p>$i->difficulty</p></</td>
					<td><p>$i->category</p></</td>
				</tr>
			</table>
			<div class='buttons-prev'>
				<form method='GET' action='edit_recipe.php'>
					<button type='submit' name='id' value='$i->id'>Edit</button>
				</form>
				<form method='POST'>
					<div style='display: flex;'>
					<input type='hidden' name='delete' value='del'>
					<button class='delete-btn' type='submit' name='id' value='$i->id'
					style='color: red'>Delete</button>
					<p class='delete-par' style='margin-left: 10px; color: red; font-weight: bold;'></p>
					</div>
			</form>
			</div>
		</div>
		_PREV;
	}
	echo "<script src='delete.js'></script></div>";
	
	if($limiter < $mapper->GetLimit())
		echo GetNavArrows($mapper, true, false);
	else
		echo GetNavArrows($mapper);
?>