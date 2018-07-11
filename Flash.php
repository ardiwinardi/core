<?php
namespace Core;

Class Flash{
	
	public static function success($msg = ''){
		$_SESSION['_success'][] = $msg;
	}
	
	public static function error($msg = ''){
		$_SESSION['_error'][] = $msg;
	}
	
	public static function getMessage(){
		$success = isset($_SESSION['_success'])? implode('<br/>',$_SESSION['_success']) : '';
		$error = isset($_SESSION['_error'])? implode('<br/>',$_SESSION['_error']) : '';
		
		if(isset($_SESSION['_success']))unset($_SESSION['_success']);
		if(isset($_SESSION['_error']))unset($_SESSION['_error']);
		return compact('success','error');
	}
}

	
?>
