<?php
namespace Core;

Class Controller{
	protected $layout = "";
	protected $_vars = array();
	protected $viewFolder = '';
	
	/**
	 * Fungsi untuk menset variable pada view
	 * @param array $vars dapat diisi string juga contoh set('title','Judul Website'); atau set(array('title'=>'Judul Website'));
	 * @param string $val
	 * @return object
	 */
	protected function set($vars = array(), $val=''){
		if(is_array($vars)){
			foreach($vars as $key=>$val) $this->_vars[$key] = $val;
		}else{
			$this->_vars[$vars] = $val;
		}
		return $this;
	}
	
	protected function View(){
		$folder = empty($this->viewFolder)? get_class($this) : $this->viewFolder;
		$view = new View($folder);
		$view->layout = $this->layout;
		$view->set($this->_vars);
		return $view;
	}
	
	protected function loadModel($Model){
		$modelName = $Model."Model"; 
		$pathModel = PATH."/app/model/{$modelName}.php";
		if(!file_exists($pathModel)){
			exit('{"Result":"ERROR","Message":"Model '.$modelName.' is not exists!"}');
		}
		require_once($pathModel);
		$this->$Model = new $modelName();
		return $this->$Model;
	}
	
	protected function loadController($Controller){
		$ctrName = $Controller."Controller"; 
		$pathCtr = PATH."/app/controller/{$ctrName}.php";
		if(!file_exists($pathCtr)){
			exit('{"Result":"ERROR","Message":"Controller '.$ctrName.' is not exists!"}');
		}
		require_once($pathCtr);
		$Controller = "C_{$Controller}";
		$this->$Controller = new $ctrName();
		return $this->$Controller;
	}
	
}
	
?>
