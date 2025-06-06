<?php
	require_once("header.php");
	
	$mapper = new RecipeMapper();
	if(!isset($_GET['id']))
		die("<h1 style='color: red;'>THIS RECIPE IS NOT AVAILABLE</h1>");
	$recipe = $mapper->Select($_GET['id']);
	
	$image = $recipe->GetImage();
	$ingList = GetIngredientList($recipe->ingredients);
	
	echo <<<_RECIPE
	<div id='recipe-header'>
		<h2>$recipe->name</h2>
			<div>
				<p>調理時間 $recipe->duration</p>
			</div>
	</div>
	
	<div id='recipe-ingredients'>
		$ingList
		<img src='$image'>
	</div>
	
	<div id='recipe-method'>
		<h2>作り方</h2>
		<pre>$recipe->method</pre>
	</div>
	_RECIPE;
	
	function GetIngredientList(array $ingList){
		$text = '<table><tr><th colspan="2"><h2>材料</h2></th></tr>';
		foreach($ingList as $i){
			$name = $i['ingredient_name'];
			$qnt = $i['quantity'];
			$text .= "<tr><th>$name</th><td>$qnt</td></tr>";
		}
		$text .= "</table>";
		return $text;
	}
?>