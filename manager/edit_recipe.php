<?php
	require_once("manager_header.php");
	
	if(!isset($_GET['id'])){
		die("no recipe selected");
	}
	else
		$id = $_GET['id'];
	
	if(isset($_POST['recip-name'])){
		if(!is_dir(IMAGE_PATH))
			mkdir(IMAGE_PATH);
		
		$preview = new PreviewMapper();
		$recipe = new RecipeMapper();
		// Image File Handling
		if($_FILES['recip-prev-img']['name'] != ''
		|| $_FILES['recip-full-img']['name'] != '')
		{
			$obj = $preview->Select($id);
			if($_FILES['recip-prev-img']['name'] != ''){
				unlink($obj->img);
				$prevImgData = GetCreateImage("recip-prev-img", $_POST['recip-name'] . "_preview");
			}
			else
				$prevImgData = $preview->Select($id)->img;
			
			$obj = $recipe->Select($id);
			if($_FILES['recip-full-img']['name'] != ''){
				unlink($obj->img);
				$imgData = GetCreateImage("recip-full-img", $_POST['recip-name']);
			}
			else
				$imgData = $recipe->Select($id)->img;
		}
		else{
			$prevImgData = $preview->Select($id)->img;
			$imgData = $recipe->Select($id)->img;
		}
		//	updating preview_recipe table
		$db_data = array();
		$db_data[] = $_POST['recip-name'];
		$db_data[] = $_POST['recip-desc'];
		$db_data[] = $prevImgData;
		$db_data[] = $_POST['recip-time'];
		$db_data[] = $_POST['recip-diff'];
		$db_data[] = $_POST['recip-cat'];
		$db_data[] = $id;
		$preview->Update($db_data);
		// updating recipe table
		$db_data = array();
		$db_data[] = $_POST['recip-meth'];
		$db_data[] = $imgData;
		$db_data[] = $id;
		$recipe->Update($db_data);
		// updating ingredient & ingredient_recipe table
		$ingredients = new IngredientMapper();
		$db_data = array();
		$db_data[] = $id;
		for($i = 0; isset($_POST["ing-$i"]); $i++){
			$ingredients->Insert([$_POST["ing-$i"]]);
			$db_data[] = ["ingredient-name" => $_POST["ing-$i"],
			"quantity" => $_POST["qnt-$i"]];
		}
		$ingRecipe = new IngRecipMapper();
		$ingRecipe->Update($db_data);
	}
	
	$mapper = new PreviewMapper();
	$recipe = $mapper->Select($_GET['id']);
	
	echo <<<_PREV
		<form method="POST" action='' enctype="multipart/form-data">
		<fieldset>
			<legend><h2>preview recipe info</h2></legend>
			<img src="$recipe->img" style='width: 150px; height: 150px;'>
			<table>
				<tr>
					<th>name</th>
					<td><input type='text' value='$recipe->name' name='recip-name'></td>
				</tr>
				<tr>
					<th>description</th>
					<td><textarea name='recip-desc' cols='50' rows='7'>$recipe->description</textarea>
				</tr>
				<tr>
					<th>preview image</th>
					<td><input type='file' value='$recipe->img' name='recip-prev-img'></td>
				</tr>
				<tr>
					<th>duration</th>
					<td><input type='text' value='$recipe->duration' name='recip-time'></td>
				</tr>
				<tr>
					<th>difficulty</th>
					<td><input type='text' value='$recipe->difficulty' name='recip-diff'></td>
				</tr>
				<tr>
					<th>category</th>
					<td><input type='text' value='$recipe->category' name='recip-cat'></td>
				</tr>
			</table>
		</fieldset>
		_PREV;
	
	$mapper = new RecipeMapper();
	$recipe = $mapper->Select($_GET['id']);
	$index = 0;
	foreach($recipe->ingredients as $i){
		$name = $i['ingredient_name'];
		$qnt = $i['quantity'];
		
		$ing_text .= <<<_INGREDIENTS
		<tr>
			<th>Ingredient</th>
			<td><input type='text' value='$name' name="ing-$index" id="ing-$index"></td>
			<th>Quantity</th>
			<td><input type='text' value='$qnt' name="qnt-$index" id="qnt-$index"></td>
		</tr>
		_INGREDIENTS;
		$index++;
	}
	echo <<<_PREV
		<fieldset>
			<legend><h2>recipe info</h2></legend>
			<table>
				<tr>
					<th>full image</th>
					<td><input type='file' value='$recipe->img' name='recip-full-img'></td>
				</tr>
				<tr>
					<th>method</th>
					<td><textarea name='recip-meth' cols='100' rows='15'>$recipe->method</textarea></td>
				</tr>
			</table>
		</fieldset>
		<fieldset>
		<legend><h2>ingredients</h2></legend>
		<button id='Addable-Form'>Add Ingredient</button>
		<button id='Removable-Form'>Remove Last Ingredient</button>
		<table id='Addable-table'>
					$ing_text
		</table>
		</fieldset>
		<br>
		<input type='submit' value='Save Changes'>
	</form>
	<script src='script.js'></script>
	_PREV;
	
?>