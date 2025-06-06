<?php
	require_once("header.php");
	
	$ingredients = new IngredientMapper();
	$ingredients->SetLimit(40);
	if(isset($_GET['of']))
		$ingredients->SetOffset($_GET['of']);
	
	if(isset($_GET['qr']))
		$ingList = $ingredients->Select($_GET['qr']);
	else
		$ingList = $ingredients->Select();
	
	echo <<<_FORM
	<form mathod='GET' id='ingredient-form'>
		<input type='text' name='qr' placeholder='例：砂糖'>
		<input type='submit' value='探す'>
	</form>
	_FORM;
	
	$limiter = 0;
	
	echo "<div id='ingredient-page'>";
	foreach($ingList as $i){
		$limiter++;
		echo <<<_ING
			<h3><a href='homepage.php?ing=$i->name'>$i->name ($i->quantity)</a></h3>
		_ING;
	}
	echo "</div>";
	
	if($limiter < $ingredients->GetLimit())
		echo GetNavArrows($ingredients, true, false);
	else
		echo GetNavArrows($ingredients);
?>