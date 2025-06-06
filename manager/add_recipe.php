<?php
	require_once("../database/dbase_utils.php");
	require_once("manager_header.php");
	
	if(isset($_POST['recip-name'])){
		if(!is_dir(IMAGE_PATH))
			mkdir(IMAGE_PATH);
		
		// preview_recipe table data
		$db_data = array();
		$db_data[] = $_POST['recip-name'];
		$db_data[] = $_POST['recip-desc'];
		$db_data[] = GetCreateImage("recip-prev-img", $_POST['recip-name'] . "_preview");
		$db_data[] = $_POST['recip-time'];
		$db_data[] = $_POST['recip-diff'];
		$db_data[] = $_POST['recip-cat'];
		$preview = new PreviewMapper();
		$id = $preview->Insert($db_data)->id;
		// recipe table data
		$db_data = array();
		$db_data[] = $id;
		$db_data[] = $_POST['recip-meth'];
		$db_data[] = GetCreateImage("recip-full-img", $_POST['recip-name']);
		$recipe = new RecipeMapper();
		$recipe->Insert($db_data);
		// ingredient & ingredient_recipe table data
		$ingredients = new IngredientMapper();
		$db_data = array();
		for($i = 0; isset($_POST["ing-$i"]); $i++){
			$ingredients->Insert([$_POST["ing-$i"]]);
			$db_data[] = ["ingredient-name" => $_POST["ing-$i"],
			"quantity" => $_POST["qnt-$i"]];
		}
		$ingRecipe = new IngRecipMapper();
		$ingRecipe->ForeignInsert($db_data, $id);
	}
	
	echo <<<_PREV
		<form method='POST' action="" enctype="multipart/form-data">
		<fieldset>
			<legend><h2>preview recipe info</h2></legend>
			<table>
				<tr>
					<th>name</th>
					<td><input type='text' name='recip-name'></td>
				</tr>
				<tr>
					<th>description</th>
					<td><textarea name='recip-desc' cols='50' rows='7'></textarea>
				</tr>
				<tr>
					<th>image preview</th>
					<td><input type='file' name='recip-prev-img'></td>
				</tr>
				<tr>
					<th>duration</th>
					<td><input type='text' name='recip-time'></td>
				</tr>
				<tr>
					<th>difficulty</th>
					<td><input type='radio' name='recip-diff' value='簡単' required>簡単</input></td>
				</tr>
				<tr>
					<th></th>
					<td><input type='radio' name='recip-diff' value='普通'>普通</input></td>
				</tr>
				<tr>
					<th></th>
					<td><input type='radio' name='recip-diff' value='難しい'>難しい</input></td>
				</tr>
				<tr>
					<th>category</th>
					<td><input type='radio' name='recip-cat' value='デサート' required>デサート</input></td>
				</tr>
				<tr>
					<th></th>
					<td><input type='radio' name='recip-cat' value='ランチ'>ランチ</input></td>
				</tr>
				<tr>
					<th></th>
					<td><input type='radio' name='recip-cat' value='ドリンク'>ドリンク</input></td>
				</tr>
			</table>
		</fieldset>
		<fieldset>
			<legend><h2>recipe info</h2></legend>
			<table>
				<tr>
					<th>image recipe</th>
					<td><input type='file' name='recip-full-img'></td>
				</tr>
				<tr>
					<th>method</th>
					<td><textarea name='recip-meth' cols='100' rows='15'></textarea></td>
				</tr>
			</table>
		</fieldset>
		<fieldset>
		<legend><h2>ingredients</h2></legend>
			<button id='Addable-Form'>Add Ingredient</button>
			<button id='Removable-Form'>Remove Last Ingredient</button>
			<table id='Addable-table'>
			</table>
		</fieldset>
		<br>
		<input type='submit' value='Create Recipe'>
	</form>
	<script src='script.js'></script>
	_PREV;
?>