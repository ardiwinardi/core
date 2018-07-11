<?php
namespace Core;
use \PDO;

Class Database{
	
	public static function connect($conf){
		$host 	= isset($conf['host'])? $conf['host'] : '';
		$user 	= isset($conf['user'])? $conf['user'] : '';
		$pass 	= isset($conf['pass'])? $conf['pass'] : '';
		$db 	= isset($conf['db'])? $conf['db'] : '';
		$charset = 'utf8';
		
		try{
			$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
			$opt = array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
				PDO::ATTR_EMULATE_PREPARES => false,
			);
			$conn = new PDO($dsn, $user, $pass, $opt);
		}
		catch(PDOException $ex){
			die("Connection failed : ".$ex->getMessage());
		}
		return $conn;
	}
	
	public static function pdo(){
		global $defaultConn;
		return $defaultConn;
	}
	
}
	
?>
