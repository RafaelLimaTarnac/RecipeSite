<?php
	class PreviewRecipe{
		public function __construct(
		public int $id,
		public string $name,
		public string $description,
		public string $img,
		public string $duration,
		public string $difficulty,
		public string $category
		){}
		/*
		*	The GetTime function removes the
		*	"../" from our image files, so they
		*	are HTML ready
		*/
		public function GetImage() : string{
			return substr($this->img, 2);
		}
		public function GetTime() : string{
			if($this->duration[1] == '0')
				return "調理時間 " . substr($this->duration, 3);
			return "調理時間 " . $this->duration;
		}
	}
	class Recipe{
		public function __construct(
			public int $id,
			public string $name,
			public string $duration,
			public string $difficulty,
			public string $method,
			public string $img,
			public array $ingredients
		){}
		public function GetImage() : string{
			return substr($this->img, 2);
		}
	}
	class Ingredient{
		public function __construct(
			public string $name,
			public int $quantity
		){}
	}
?>