<?php
/**
 * Kelas yang berperan menjadi entitas sebagai penghubung antara table pada database dengan aplikasi
 */
namespace Core;
use \PDO;

Class Model implements \IteratorAggregate{
	
	/**
	 * @var string Disii dengan nama table
	 */
	protected $table;
	
	/**
	 * @var string Disii dengan alias dari nama table
	 */
	protected $alias = "A";
	
	/**
	 * @var string Disii dengan nama primarykey table
	 */
	protected $primaryKey;
	
	/**
	 * @var string
	 */
	private $query = "";
	
	/**
	 * @var string
	 */
	private $select = "";
	
	/**
	 * @var string
	 */
	private $where = "";
	
	/**
	 * @var string
	 */
	private $order = "";
	
	/**
	 * @var string
	 */
	private $group = "";
	
	/**
	 * @var string
	 */
	private $limit = "";
	
	/**
	 * @var string
	 */
	private $join = "";
	
	/**
	 * @var object
	 */
	protected $pdo;
	
	public function __construct($table='', $primaryKey=''){
		global $dbconf;
		
		if(!empty($table)) $this->table = $table;
		if(!empty($primaryKey)) $this->primaryKey = $primaryKey;
		$this->db = $dbconf['db'];
		$this->pdo = Database::pdo();
	}
	
	/**
	 * Fungsi set alias
	 * @return void
	 */
    public function set_alias($alias) {
		$this->alias = $alias;
		return $this;
    }
    
	/**
	 * Fungsi overide
	 * @return object
	 */
    public function getIterator() {
		$data = $this->executeQueryBuilding();
		
		$d = array();
		foreach($data as $row){
			$d[] = $row;
		}
		return new MyIterator($d);
    }
    
    /**
	 * Fungsi untuk membersihkan query
	 * @return void
	 */
    private function clear(){
		$this->query = "";
		$this->select = "";
		$this->where = "";
		$this->group = "";
		$this->order = "";
		$this->limit = "";
		$this->join = "";
	}
	
	/**
	 * Fungsi untuk mengambil query string yang belum dieksekusi
	 * @return string
	 */
	public function __toString(){
		return $this->_getQuery();
	}
	
	/**
	 * Fungsi untuk mengambil query string yang sedang dieksekusi
	 * @return string
	 */
	public function getSql(){
		return $this->_getQuery();
	}
	
	/**
	 * Fungsi untuk select query
	 * @param array $select
	 * @return object Model
	 */
	public function select($select = array()){
		$this->query = "SELECT * FROM {$this->table}";
		$this->select = implode(",",$select);
		return $this;
	}
	
	/**
	 * Fungsi untuk mengisi filter where
	 * @param string $where diisi dengan filter misal username = 'ardi'
	 * @return object Model
	 */
	public function where($where = ""){
		$this->where .= $where;
		return $this;
	}
	
	/**
	 * Fungsi untuk limit query
	 * @param string $limit
	 * @return object Model
	 */
	public function limit($offset=0, $limit = 100){
		$this->limit = "{$offset},{$limit}";
		return $this;
	}
	
	/**
	 * Fungsi untuk group query
	 * @param string $group
	 * @return object Model
	 */
	public function group($group = ""){
		$this->group = $group;
		return $this;
	}
	
	/**
	 * Fungsi untuk order query
	 * @param string $order
	 * @return object Model
	 */
	public function order($order = ""){
		$this->order = $order;
		return $this;
	}
	
	public function innerJoin($table, $conditions = array()){
		return $this->join('inner', $table, $conditions);
	}
	
	public function leftJoin($table, $conditions = array()){
		return $this->join('left', $table, $conditions);
	}
	
	/**
	 * Fungsi untuk join query
	 * @param string $type Diisi dengan inner, left, right
	 * @param string $table Diisi nama table yang dijoin 
	 * @param array $conditions Diisi misal array('username'=>'ardi')
	 * @return object Model
	 */
	private function join($type, $table, $conditions = array()){
		$cond = "";
		foreach($conditions as $key=>$val){
			$cond .= "{$key} = {$val} AND ";
		}
		$cond = substr($cond, 0, strlen($cond)-4);
		$this->join .= strtoupper($type)." JOIN {$table} ON {$cond}";
		return $this;
	}
	
	/**
	 * Fungsi untuk menghasilkan count query
	 * @return int Total count
	 */
	public function count(){
		$this->select = "COUNT(*) as COUNT";
		$count = $this->executeQueryBuilding()->fetchColumn();
		return $count;
	}
	
	private function _getQuery(){
		$query = "";
		if(!empty($this->query)){
			$query .= $this->query;
		}
		if(!empty($this->join)){
			$query .= " AS {$this->alias} {$this->join}";
		}
		if(!empty($this->where)){
			$query .= " WHERE {$this->where}";
		}
		if(!empty($this->group)){
			$query .= " GROUP BY {$this->group}";
		}
		if(!empty($this->order)){
			$query .= " ORDER BY {$this->order}";
		}
		if(!empty($this->limit)){
			$query .= " LIMIT {$this->limit}";
		}
		if(!empty($this->select)){
			$query = str_replace("*",$this->select, $query)." ";
		}
		$this->clear();
		return $query;
	}
	
	/**
	 * Fungsi untuk mengambil 1 row data berdasar id data
	 * @param string $id Diisi primarykey
	 * @return object
	 */
	public function get($id){
		$this->clear();
		$this->query = "SELECT * FROM {$this->table}";
		$this->where("{$this->primaryKey} = '{$id}'");
		return $this->first();
		
	}
	
	/**
	 * Fungsi untuk mengambil data 1 row dari query yang berupa objek
	 * @return object
	 */
	public function first(){
		return $this->executeQueryBuilding()->fetch();
	}
	
	/**
	 * Fungsi untuk mengeksekusi queryString yang dikirim via parameter
	 * @param string $query diisi dengan query string
	 * @return object
	 */
	public function query($query){
		try{
			$res = $this->pdo->query($query);
		}
		catch(PDOException $ex){
			die("Error Query : ".get_class($this)." [{$query}] ".$ex->getMessage());
		}
		
		$this->lastQuery[] = $query;
		return $res;
	}
	
	/**
	 * Fungsi untuk mengeksekusi query yang digenerate oleh query builder
	 * @return object
	 */
	private function executeQueryBuilding(){
		$query = $this->_getQuery();
		return $this->query($query);
	}
	
	/**
	 * Fungsi untuk patching/ menampung / menyatukan dan memfilter data berdasarkan objek
	 * @param object $exEntity Diisi dengan object yang akan disatukan
	 * @param object $data Diisi dengan object lain yang akan disatukan
	 * @return object
	 */
	public function patchEntity($exEntity, $data){
		$entity = $this->newEntity();
		foreach($entity as $key=>$val){
			$entity->$key = isset($data->$key)? $data->$key : (isset($exEntity->$key)? $exEntity->$key : $val);
		}
		return $entity;
	}
	
	/**
	 * Fungsi untuk mengambil last query
	 * @return string
	 */
	
	public function getLastQuery(){
		return debug($this->lastQuery, true);
	}
		
	/**
	 * Fungsi untuk check data baru atau bukan
	 * @param object $id
	 * @return boolean
	 */
	private function _isNew($entity,$primaryKey){
		/*jika tidak memiliki primary key maka data dianggap new*/
		if(count($primaryKey)==0) return true;
		
		$where = ' WHERE '.$this->_setWherePrimary($entity, $primaryKey);
		$query = "SELECT * FROM {$this->table} {$where}";
		$data = $this->query($query);
		return $data->rowCount()==0;
	}
	
	private function _setWherePrimary($entity,$primaryKey){
		$where= array();
		foreach($primaryKey as $key){
			$where[] = sprintf(" {$key} = '%s' ",$entity->{$key});
		}
		return implode(' AND ',$where);
	}
	
	private function _getPrimaryKey(){
		$query = "SHOW KEYS FROM {$this->table} WHERE Key_name = 'PRIMARY'";
		$data = $this->query($query);
		$pk = array();
		foreach($data as $row){
			$pk[] = $row->Column_name;
		}
		return $pk;
	}
	
	private function filterEntity($filledEntity){
		$entity = $this->newEntity();
		foreach($entity as $key=>$val){
			$entity->$key = isset($filledEntity->$key)? $filledEntity->$key : '';
		}
		return $entity;
	}
	
	/**
	 * Fungsi untuk insert / update data
	 * @param object $entity
	 * @return boolean
	 */
	public function save($entity){
		$entity = $this->filterEntity($entity);
		$primaryKey = $this->_getPrimaryKey();
		$valueBinding = array();
		
		/*check new or old data*/
		if($this->_isNew($entity, $primaryKey)){
			$field = array_keys((array)$entity);
			$field = implode(',',$field);
			
			$value = array();
			foreach($entity as $key=>$val){
				$key = ':'.$key;
				$value[] = $key;
				$valueBinding[$key] = $val;
			}
			$value = implode(',',$value);
			$query = "INSERT INTO {$this->table} ({$field}) VALUES ($value) ";
			
		}else{
			$set = array();
			foreach($entity as $key=>$val){
				if(!in_array($key,$primaryKey)){
					$set[] = "{$key} = :{$key}";
					$valueBinding[':'.$key] = $val;
				}
			}
			$set = implode(',',$set);
			$where = ' WHERE '.$this->_setWherePrimary($entity, $primaryKey);
			$query = "UPDATE {$this->table} SET {$set} {$where} ";
		}
		
		try{
			$res = $this->pdo->prepare($query)->execute($valueBinding);
		}
		catch(PDOException $ex){
			die("Error Query : ".get_class($this)." [{$query}] ".$ex->getMessage());
		}
		
		return $res;
	}
	
	/**
	 * Fungsi untuk delete data berdasar primary key
	 * @param string $id Diisi dengan id data
	 * @return boolean
	 */
	public function delete($id=''){
		if(empty($id)) return false;
		
		$where = sprintf(" {$this->primaryKey} = '{$id}'", $id);
		$query = "DELETE FROM {$this->table} WHERE {$where} ";
		return $this->query($query);
	}
	
	/**
	 * Fungsi untuk delete data secara custom where
	 * @param string $where
	 * @return boolean
	 */
	public function deleteWhere($where=''){
		if(empty($where)) return false;
		
		$query = "DELETE FROM {$this->table} WHERE {$where} ";
		return $this->query($query);
	}
	
	/**
	 * Fungsi untuk menginisiasi objek baru dari suatu model
	 * @return object
	 */
	public function newEntity(){
		return $this->_initEntity();
	}
	
	protected function _initEntity(){
		$entity = array();
		$path = PATH.'/cache/'.md5($this->table);
		
		if(file_exists($path)){
			$s = file_get_contents($path);
			$entity = unserialize($s);
		}else{
			$res = $this->query("SELECT * FROM {$this->table} LIMIT 0,1");
			
			foreach(range(0, $res->columnCount() - 1) as $x){
				$col = $res->getColumnMeta($x);
				$entity[$col['name']] = '';
			}
			file_put_contents($path, serialize($entity = (object) $entity));
		}
		
		return $entity;
	}
	
}
	
?>
