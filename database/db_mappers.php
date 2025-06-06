<?php
	require_once("dbase_utils.php");
	require_once("recipe_classes.php");

	// BASE MAPPER
	abstract class DataMapper{
		protected $pdo;
		protected $limit = 100, $offset = 0;
		
		public function __construct(){
			$utils = new Utils();
			$this->pdo = $utils->GetInstance()->GetPdo();
		}
		public function Select(mixed $key = null){
			if(!isset($key))
				$obj = $this->DoSelect();
			else
				$obj = $this->DoKeySelect($key);
			
			return $obj;
		}
		public function Insert(array $raw){
			$obj = $this->DoInsert($raw);
			return $obj;
		}
		/*
		*	Foreign insert
		*	(insert values based on an existing id)
		*/
		public function ForeignInsert(array $raw, mixed $id){
			$obj = $this->DoForeignInsert($raw, $id);
			return $obj;
		}
		public function Update(array $raw){
			$obj = $this->DoUpdate($raw);
			return $obj;
		}
		public function Remove(mixed $id){
			$this->DoRemove($id);
		}
		/*
		*	SET | GET FUNCTIONS
		*/
		public function SetOffset($_offset): void{
			$this->offset = $_offset;
		}
		public function SetLimit($_limit): void{
			$this->limit = $_limit;
		}
		public function GetOffset() : int{
			return $this->offset;
		}
		public function GetLimit() : int{
			return $this->limit;
		}
		
		/*
		*	ABSTRACT FUNCTIONS
		*/
		abstract protected function DoSelect();
		abstract protected function DoInsert(array $raw);
		abstract protected function DoUpdate(array $raw);
		abstract protected function DoRemove(mixed $id);
		abstract protected function DoKeySelect(mixed $key);
		abstract protected function DoForeignInsert(array $raw, mixed $id);
	}
	// PREVIEW MAPPER
	class PreviewMapper extends DataMapper{
		private \PDOStatement $selectStmt;
		private \PDOStatement $selectKeyStmt;
		private \PDOStatement $selectCatStmt;
		private \PDOStatement $selectIngStmt;
		private \PDOStatement $selectNameStmt;
		private \PDOStatement $insertStmt;
		private \PDOStatement $updateStmt;
		private \PDOStatement $removeStmt;
		public function __construct(){
			parent::__construct();
			
			$this->selectStmt = $this->pdo->prepare(
			"select * from preview_recipe limit ? offset ?;"
			);
			$this->insertStmt = $this->pdo->prepare(
			"insert into preview_recipe(name, description," .
			"image_file, duration, difficulty, category) values(?,?,?,?,?,?);"
			);
			$this->selectKeyStmt = $this->pdo->prepare(
			"select * from preview_recipe where id=?;"
			);
			$this->selectCatStmt = $this->pdo->prepare(
			"select * from preview_recipe where category=? limit ? offset ?;"
			);
			$this->selectNameStmt = $this->pdo->prepare(
			"select * from preview_recipe where name like ? limit ? offset ?;"
			);
			$this->selectIngStmt = $this->pdo->prepare(
			"select distinct id, name, description, image_file, duration, difficulty, 
			category from preview_recipe join ingredient_recipe 
			where ingredient_recipe.ingredient_name = ? 
			and ingredient_recipe.recipe_id = preview_recipe.id 
			limit ? offset ?;"
			);
			
			$this->insertStmt = $this->pdo->prepare(
			"insert into preview_recipe(
			name, description, image_file, 
			duration, difficulty, category)
			values(?, ?, ?, ?, ?, ?);"
			);
			$this->updateStmt = $this->pdo->prepare(
			"update preview_recipe set
			name = ?, description = ?, image_file = ?, 
			duration = ?, difficulty = ?, category = ? 
			where id = ?;"
			);
			$this->removeStmt = $this->pdo->prepare(
			"delete from preview_recipe where id = ?;"
			);
		}
		protected function DoSelect(){
			$obj = [];
			$result = $this->selectStmt->execute([$this->limit, $this->offset]);
			
			while($row = $this->selectStmt->fetch()){
				$obj[] = new PreviewRecipe(
					$row['id'],
					$row['name'],
					$row['description'],
					$row['image_file'],
					$row['duration'],
					$row['difficulty'],
					$row['category']
				);
			}
			return $obj;
		}
		/*	PreviewMapper
		*	DoKeySelect
		*
		*	Since the Preview Mapper supports query by
		*	ingredient, category, name and id, i've made
		*	the DoKeySelect expandable, the first parameter
		*	can be a Map. The map's key defines the search type.
		*/
		protected function DoKeySelect(mixed $key){
			if(!is_array($key)){
				$this->selectKeyStmt->execute([$key]);
				$currStmt = $this->selectKeyStmt;
			}
			else{
				if(isset($key['category'])){
					$this->selectCatStmt->execute([$key['category'], $this->limit, $this->offset]);
					$currStmt = $this->selectCatStmt;
				}
				else if(isset($key['ingredient'])){
					$this->selectIngStmt->execute([$key['ingredient'], $this->limit, $this->offset]);
					$currStmt = $this->selectIngStmt;
				}
				else if(isset($key['name'])){
					$this->selectNameStmt->execute(['%' . $key['name'] . '%', $this->limit, $this->offset]);
					$currStmt = $this->selectNameStmt;
				}
			}
			
			while($row = $currStmt->fetch()){
				$obj[] = new PreviewRecipe(
					$row['id'],
					$row['name'],
					$row['description'],
					$row['image_file'],
					$row['duration'],
					$row['difficulty'],
					$row['category']
				);
			}
			
			if(!is_array($key))
				return $obj[0];
			return $obj;
		}
		protected function DoInsert(array $raw){
			for($i = 0; $i >= sizeof($raw); $i++){
				$raw[$i] = $this->pdo->quote($raw[$i]);
			}
			$this->insertStmt->execute($raw);
			
			return $this->DoKeySelect($this->pdo->lastInsertId());
		}
		protected function DoForeignInsert(array $raw, mixed $id){
			throw new Exception("|Objeto nao usa foreign insert|");
		}
		protected function DoUpdate(array $raw){
			$this->updateStmt->execute($raw);
		}
		protected function DoRemove(mixed $id){
			$this->removeStmt->execute([$id]);
		}
	}
	// RECIPE MAPPER
	class RecipeMapper extends DataMapper{
		private \PDOStatement $selectStmt;
		private \PDOStatement $selectKeyStmt;
		private \PDOStatement $ingListSmtm;
		private \PDOStatement $insertStmt;
		private \PDOStatement $updateStmt;
		public function __construct(){
			parent::__construct();
			
			$this->selectStmt = $this->pdo->prepare(
			"select * from recipe;"
			);
			$this->selectKeyStmt = $this->pdo->prepare(
			"select
			preview_recipe.name,
			preview_recipe.difficulty,
			preview_recipe.duration,
			recipe.id,
			recipe.method,
			recipe.image_file 
			from preview_recipe 
			inner join recipe 
			on preview_recipe.id = recipe.id 
			where recipe.id = ?;"
			);
			$this->ingListSmtm = $this->pdo->prepare(
			"select
			ingredient_recipe.ingredient_name,
			ingredient_recipe.quantity
			from ingredient_recipe
			inner join recipe
			on recipe.id = ingredient_recipe.recipe_id 
			where recipe.id = ?;"
			);
			$this->insertStmt = $this->pdo->prepare(
			"insert into recipe(id, method, image_file)
			values(?, ?, ?);"
			);
			$this->updateStmt = $this->pdo->prepare(
			"update recipe set
			method = ?, image_file = ? 
			where id = ?;"
			);
		}
		
		protected function DoSelect(){
			throw new Exception("|Não usamos select * para o recipe mapper|");
		}
		protected function DoKeySelect(mixed $key){
			$this->selectKeyStmt->execute([$key]);
			$info_result = $this->selectKeyStmt->fetch();
			$this->ingListSmtm->execute([$key]);
			$ingredients = Fetcher($this->ingListSmtm);
			
			$recipe = new Recipe(
			$info_result['id'],
			$info_result['name'],
			$info_result['duration'],
			$info_result['difficulty'],
			$info_result['method'],
			$info_result['image_file'],
			$ingredients
			);
			
			return $recipe;
		}
		protected function DoInsert(array $raw){
			for($i = 0; $i >= sizeof($raw); $i++){
				$raw[$i] = $this->pdo->quote($raw[$i]);
			}
			$this->insertStmt->execute($raw);
		}
		protected function DoForeignInsert(array $raw, mixed $id){
			throw new Exception("|Objeto nao usa foreign insert|");
		}
		protected function DoUpdate(array $raw){
			$this->updateStmt->execute($raw);
		}
		protected function DoRemove(mixed $id){
			throw new Exception("Object has no DELETE operation YET");
		}
	}
	// INGREDIENT RECIPE MAPPER
	class IngRecipMapper extends DataMapper{
		private \PDOStatement $selectStmt;
		private \PDOStatement $selectKeyStmt;
		private \PDOStatement $insertStmt;
		private \PDOStatement $removeStmt;
		
		public function __construct(){
			parent::__construct();
			$this->insertStmt = $this->pdo->prepare(
			"insert into ingredient_recipe(recipe_id,
			quantity, ingredient_name) 
			values(?, ?, ?);"
			);
			$this->selectKeyStmt = $this->pdo->prepare(
			"select 
			ingredient_name, 
			quantity 
			from ingredient_recipe
			where recipe_id = ?;"
			);
			$this->removeStmt = $this->pdo->prepare(
			"delete from ingredient_recipe where recipe_id = ?;"
			);
		}
		
		protected function DoInsert(array $raw){
			throw new Exception("|Não usamos insert base para o ingredient recipe mapper|");
		}
		protected function DoForeignInsert(array $raw, mixed $id){
			for($i = 0; $i < sizeof($raw); $i++){
				$ing_name = $raw[$i]['ingredient-name'];
				$qnt = $raw[$i]['quantity'];
				$this->insertStmt->execute([$id, $qnt, $ing_name]);
			}
		}
		protected function DoSelect(){
			throw new Exception("|Não usamos select * para o ingredient recipe mapper|");
		}
		protected function DoKeySelect(mixed $key){
			$this->selectKeyStmt->execute([$key]);
			while($row = $this->selectKeyStmt->fetch()){
				$ingredients[] =
				[ "ingredient" => $row['ingredient_name'],
				"quantity" => $row['quantity']];
			}
			return $ingredients;
		}
		protected function DoUpdate(array $raw){
			$id = $raw[0];
			unset($raw[0]);
			sort($raw);
			$this->removeStmt->execute([$id]);
			$this->DoForeignInsert($raw, $id);
		}
		protected function DoRemove(mixed $id){
			throw new Exception("Object has no DELETE operation YET");
		}
	}
	// INGREDIENT MAPPER
	class IngredientMapper extends DataMapper{
		private \PDOStatement $selectStmt;
		private \PDOStatement $selectKeyStmt;
		private \PDOStatement $insertStmt;
		private \PDOStatement $checkNullStmt;
		private \PDOStatement $removeStmt;
		
		public function __construct(){
			parent::__construct();
			
			$this->selectStmt = $this->pdo->prepare(
			"select ingredient_name, count(ingredient_name) as count 
			from ingredient_recipe group by ingredient_name 
			order by count desc limit ? offset ?;"
			);
			$this->selectKeyStmt = $this->pdo->prepare(
			"select ingredient_name, count(ingredient_name) as count 
			from ingredient_recipe where ingredient_name like ? 
			group by ingredient_name order by count desc limit ? offset ?;"
			);
			$this->insertStmt = $this->pdo->prepare(
			"insert ignore into ingredient values(?);"
			);
			$this->checkNullStmt = $this->pdo->prepare(
			"select name from ingredient 
			left join ingredient_recipe 
			on ingredient.name = ingredient_recipe.ingredient_name 
			where ingredient_recipe.ingredient_name is null;"
			);
			$this->removeStmt = $this->pdo->prepare(
			"delete from ingredient where name = ?;"
			);
		}
		
		protected function DoInsert(array $raw){
			$this->insertStmt->execute($raw);
		}
		protected function DoSelect(){
			$this->selectStmt->execute([$this->limit, $this->offset]);
			while($row = $this->selectStmt->fetch())
				$obj[] = new Ingredient($row['ingredient_name'], $row['count']);
			return $obj;
		}
		protected function DoKeySelect(mixed $key){
			$key = "%" . $key . "%";
			$this->selectKeyStmt->execute([$key, $this->limit, $this->offset]);
			while($row = $this->selectKeyStmt->fetch())
				$obj[] = new Ingredient($row['ingredient_name'], $row['count']);
			return $obj;
		}
		protected function DoForeignInsert(array $raw, mixed $id){}
		protected function DoUpdate(array $raw){}
		protected function DoRemove(mixed $id){
			$this->checkNullStmt->execute();
			while($row = $this->checkNullStmt->fetch(PDO::FETCH_NUM)){
				foreach($row as $i){
					$this->removeStmt->execute([$i]);
				}
			}
		}
	}
?>