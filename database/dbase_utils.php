<?php
	require_once("login_db.php");

	try{
		$pdo = new PDO($attr, $user, $pass, $opts);
	}
	catch(PDOException $e){
		throw new PDOException($e->getMessage(), (int)$e->getCode());
	}
	/*
	*	Fetcher gets values from multiple rows from a single statement
	*	Returns an array of fetched values
	*/	
	function Fetcher(\PDOStatement $fetcher) : array{
		while($row = $fetcher->fetch())
			$results[] = $row;
		
		return $results;
	}
	/*
	*	SINGLETON
	*	(i store the system's pdo here)
	*/
	class Utils{
		public static Utils $instance;
		private static PDO $pdo;
		public function __construct(){}
		public static function GetInstance() : Utils{
			if(empty(self::$instance))
				self::$instance = new Utils();
			global $pdo;
			self::$pdo = $pdo;
			
			return self::$instance;
		}
		public function GetPdo() : PDO{
			return self::$pdo;
		}
	}
?>