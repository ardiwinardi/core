<?php
namespace Core;

Class Request{

	public static function data($id=""){
		return empty($id)? (object) $_REQUEST : (isset($_REQUEST[$id])?$_REQUEST[$id]:null);
	}
	
	public static function action(){
		$action = explode(".",$_POST['action']);
		if(count($action)!=2){
			exit('{"Result":"ERROR","Message":"Action is not valid!"}');
		}
		return $action[1];
	}
	
	public static function type($type=''){
		return empty($type)? $_SERVER['REQUEST_METHOD'] : $_SERVER['REQUEST_METHOD'] === strtoupper($type);
	}
	
}
	
?>
