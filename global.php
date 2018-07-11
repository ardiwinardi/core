<?php
function csrf_token(){
	return isset($_SESSION['csrf_token'])? $_SESSION['csrf_token'] : '';
}

function debug($arr, $return = false){
	$debug = '<pre>'.print_r($arr, true).'</pre>';
	if($return) return $debug;
	echo $debug;
}

function UUID($sDelim = ''){
	return sprintf('%04x%04x%s%04x%s%03x4%s%04x%s%04x%04x%04x',
		mt_rand(0, 65535), mt_rand(0, 65535),$sDelim,mt_rand(0, 65535),$sDelim,mt_rand(0, 4095),
		$sDelim,bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),$sDelim,
		mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)
	);
}

function action($action){
	$url = WEB_ROOT.'/'.$action;
	return $url;
}

function redirect($action){
	$url = (substr($action,0,4) == 'http')? $action : action($action);
	ob_clean();
	header('location:'.$url);
	exit;
}

function getUserlogin(){
	return isset($_SESSION['_login_user_email'])? $_SESSION['_login_user_email']: '';
}

function getUserId(){
	return isset($_SESSION['_login_user_id'])? $_SESSION['_login_user_id'] : '';
}

function create_slug($string){
	$string = strtolower($string);
	$slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
	return $slug;
}
?>
