<?php
	require_once("header.php");
	
	$preview = new PreviewMapper();
	$preview->SetLimit(8);
	if(isset($_GET['of'])){
		$preview->SetOffset($_GET['of']);
	}
	
	if(isset($_GET['cat'])){
		$qrParam['category'] = $_GET['cat'];
		$recipes = $preview->Select($qrParam);
	}
	else if(isset($_GET['ing'])){
		$qrParam['ingredient'] = $_GET['ing'];
		$recipes = $preview->Select($qrParam);
	}
	else{
		$recipes = $preview->Select();
	}
	
	$limiter = 0;
	
	echo "<div id='recipe-container'>";
	foreach($recipes as $i){
		$limiter++;
		
		$img = $i->GetImage();
		$time = $i->GetTime();
		$diff_line;
		switch($i->difficulty){
			case '簡単':
				$diff_line = "<p style='color:#31bf31'>簡単</p>";
				break;
			case '普通':
				$diff_line = "<p style='color:#ff7919'>普通</p>";
				break;
			case '難しい':
				$diff_line = "<p style='color:red'>難しい</p>";
				break;
			default:
				$diff_line = 'ERROR';
				break;
		}
		
		echo <<<_TEST
		<a class='preview_recipe' href='recipe.php?id=$i->id'>
			<img src='$img'>
			<h4 class='title'>$i->name</h4>
				<div class='recip-time-diff'>
					$diff_line
					<p>$time</p>
				</div>
			<p class='description'>$i->description</p>
		</a>
		_TEST;
	}
	echo "</div>";
	
	if($limiter < $preview->GetLimit())
		echo GetNavArrows($preview, true, false);
	else
		echo GetNavArrows($preview);
?>