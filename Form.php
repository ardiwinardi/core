<?php
namespace Core;

Class Form{
	
	private $_entity;
	private $_editable;
	
	public function __construct($entity = "", $editable = true){
		$this->_entity = $entity;
		$this->_editable = $editable;
		
		echo $this->inputHidden('_token',['value'=>csrf_token()]);
	}
	
	private function _setValue($id, $attr = array()){
		return (isset($this->_entity->$id)? $this->_entity->$id : (isset($attr['value'])?$attr['value']:''));
	}

	public function inputText($id, $attr = array(), $editable = true){
		$attr['value'] = $this->_setValue($id, $attr);
		
		if(!$this->_editable || !$editable) $attr['readonly']='';
		$params = array(
			'id'=>$id,
			'attr'=>$attr,
			'type'=>'input'
		);
		return self::_formGroup($params);
	}
	
	public function inputPassword($id, $attr = array(), $editable = true){
		$attr['value'] = $this->_setValue($id, $attr);
		
		if(!$this->_editable || !$editable) $attr['readonly']='';
		$params = array(
			'id'=>$id,
			'attr'=>$attr,
			'type'=>'password'
		);
		return self::_formGroup($params);
	}
	
	public function inputHidden($id, $attr = array()){
		$value = $this->_setValue($id, $attr);
		$name = $id;
		$id = str_replace('[]','',$id);
		return "<input type='hidden' id='{$id}' name='{$name}' value='{$value}'>";
	}
	
	public function inputTextAction($id, $attr = array(), $editable = true){
		$attr['value'] = $this->_setValue($id, $attr);
		if(!$this->_editable || !$editable) $attr['readonly']='';
		$params = array(
			'id'=>$id,
			'attr'=>$attr,
			'type'=>'input'
		);
		return self::_formGroupAction($params);
	}
	
	public function inputTextarea($id, $attr = array(),$editable = true){
		$attr['value'] = $this->_setValue($id, $attr);
		if(!$this->_editable || !$editable) $attr['readonly']='';
		$params = array(
			'id'=>$id,
			'attr'=>$attr,
			'type'=>'textarea'
		);
		return self::_formGroup($params);
	}
	
	public function select($id, $list = array(), $attr = array(), $editable = true){
		$attr['value'] = $this->_setValue($id, $attr);
		if(!$this->_editable || !$editable) $attr['readonly']='';
		$params = array(
			'id'=>$id,
			'attr'=>$attr,
			'list'=>$list,
			'type'=>'select'
		);
		return self::_formGroup($params);
	}
	
	private static function _formGroup($params){
		$id = $params['id'];
		$attr = $params['attr'];
		$type = "_".$params['type'];
		$attr['placeholder'] = isset($attr['placeholder'])? $attr['placeholder'] : (isset($attr['label'])? $attr['label'] : ucwords($id));
		$attr['placeholder'] = empty($attr['placeholder'])? "" : strip_tags($attr['placeholder']);
		
		$fieldParam['id'] = $id;
		$fieldParam['attr'] = $attr;
		if(isset($params['list'])) $fieldParam['list'] = $params['list'];
		
		$label = isset($attr['label'])? $attr['label'] : ucfirst($id);
		$attr['placeholder'] = $label;
		return empty($label)? self::$type($fieldParam) : '<div class="form-group">
			'.self::_label($id, $label).'
			'.self::$type($fieldParam).'
		</div>';
	}
	
	private static function _formGroupAction($params){
		$id = $params['id'];
		$attr = $params['attr'];
		$type = "_".$params['type'];
		$attr['placeholder'] = isset($attr['placeholder'])? $attr['placeholder'] : (isset($attr['label'])? $attr['label'] : ucwords($id));
		$attr['placeholder'] = empty($attr['placeholder'])? "" : strip_tags($attr['placeholder']);
		
		$btnLabel = isset($attr['btn-label'])? $attr['btn-label'] : "";
		$btnAction = isset($attr['btn-action'])? $attr['btn-action'] : "void(0)";
		
		unset($attr['btn-label']);
		unset($attr['btn-action']);
		
		$fieldParam['id'] = $id;
		$fieldParam['attr'] = $attr;
		if(isset($params['list'])) $fieldParam['list'] = $params['list'];
		
		$label = isset($attr['label'])? $attr['label'] : ucfirst($id);
		$attr['placeholder'] = $label;
		return '<div class="form-group">
			'.self::_label($id, $label).'
			<div class="input-group">
				'.self::$type($fieldParam).'
				<span class="input-group-btn">
					<button class="btn btn-primary btn-sm" id="'.$id.'-action" onclick="javascript:'.$btnAction.'" type="button">'.$btnLabel.'</button>
				</span>
			</div>
		</div>';
	}
	
	private static function _label($id, $label, $attr = array()){
		$html = "<label for='{$id}'";
		foreach($attr as $key=>$val){
			$html .= "{$key}='{$val}' ";
		}
		$html .= ">{$label}</label>";
		return $html;
	}
	
	private static function _input($fieldParam){
		$id = $fieldParam['id'];
		$attr = $fieldParam['attr'];
		
		$name = $id;
		$id = str_replace('[]','',$id);
		
		$html = "<input type='text' id='{$id}' name='{$name}' ";
		$attr['class'] = (isset($attr['class'])? $attr['class']." " : "")."form-control input-sm";
		foreach($attr as $key=>$val){
			$html .= "{$key}='{$val}' ";
		}
		$html .= ">";
		return $html;
	}
	
	private static function _password($fieldParam){
		$id = $fieldParam['id'];
		$attr = $fieldParam['attr'];
		
		$name = $id;
		$id = str_replace('[]','',$id);
		
		$html = "<input type='password' id='{$id}' name='{$name}' ";
		$attr['class'] = (isset($attr['class'])? $attr['class']." " : "")."form-control input-sm";
		foreach($attr as $key=>$val){
			$html .= "{$key}='{$val}' ";
		}
		$html .= ">";
		return $html;
	}
	
	private static function _textarea($fieldParam){
		$id = $fieldParam['id'];
		$attr = $fieldParam['attr'];
		$html = "<textarea id='{$id}' name='{$id}' ";
		$attr['class'] = (isset($attr['class'])? $attr['class']." " : "")."form-control input-sm";
		$value = isset($attr['value'])?$attr['value']:"";unset($attr['value']);
		foreach($attr as $key=>$val){
			$html .= "{$key}='{$val}' ";
		}
		$html .= ">{$value}</textarea>";
		return $html;
	}
	
	private static function _select($fieldParam){
		$id = $fieldParam['id'];
		$attr = $fieldParam['attr'];
		$list = $fieldParam['list'];
		
		$name = $id;
		$id = str_replace('[]','',$id);
		
		$html = "<select id='{$id}' name='{$name}' ";
		$attr['class'] = (isset($attr['class'])? $attr['class']." " : "")."form-control input-sm";
		$value = isset($attr['value'])?$attr['value']:"";unset($attr['value']);
		$readonly = isset($attr['readonly'])? "disabled":"";unset($attr['readonly']);
		foreach($attr as $key=>$val){
			$html .= "{$key}='{$val}' ";
		}
		$html.= ">";
		foreach($list as $key=>$val){
			$html.= "<option value='{$key}' ".($value == $key? "selected" : $readonly).">{$val}</option>";
		}
		$html.= "</select>";
		return $html;
	}
}

	
?>
