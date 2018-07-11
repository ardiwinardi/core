<?php
namespace Core;

Class View{
	
	public $layout = "";
	public $message = "message";
	private $_viewFolder;
	private $_layoutFolder;
	private $_vars = array();
	
	public function __construct($subFolder = ''){
		$subFolder = empty($subFolder)? '' : '/'.str_replace('Controller','',$subFolder);
		$this->_viewFolder = PATH."/app/view".$subFolder;
		$this->_layoutFolder = PATH."/app/view/Layout";
	}
	
	public function load($view, $vars = array()){
		$_SESSION['csrf_token'] = UUID();
		
		$path = $this->_viewFolder."/{$view}.php";
		if($html = $this->get_html($path,$vars)){
			$vars['content'] = $html;
			echo $this->layout($vars);
		}else{
			echo "view {$path} not exists!";
		}
	}
	
	private function layout($vars = array()){
		global $a,$m;
		
		$path = $this->_layoutFolder."/{$this->message}.php";
		$vars['message'] = $this->get_html($path,Flash::getMessage());
		$vars['a'] = $a;
		$vars['m'] = $m;
		
		$path = $this->_layoutFolder."/{$this->layout}.php";
		if($html = $this->get_html($path, $vars)){
			return $html;
		}else{
			echo 'layouts not exists!';
		}
	}
	
	private function get_html($path, $vars = array()){
		$html = false;
		
		if (file_exists($path)) {
			$vars = array_merge($this->_vars, $vars);
			extract($vars);
			ob_start();
			include($path);
			$html = ob_get_contents();
			ob_end_clean();
		}		
		return $html;
	}
	
	public function set($vars = array()){
		foreach($vars as $key=>$val)
			$this->_vars[$key] = $val;
		return $this;
	}
}
?>
