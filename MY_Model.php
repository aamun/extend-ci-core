<?php
class My_Model extends CI_Model implements ArrayAccess{
	
	protected $tableName;
	protected $primaryKey;
	protected $record;

	public function __construct(){
		parent::__construct();
	}

	public function getAll($limit = null, $offset = null){
		$query = $this->db->get($this->tableName, $limit, $offset);
		return $query->result();
	}

    public function getAllAsArray($limit = null, $offset = null){
        $query = $this->db->get($this->tableName, $limit, $offset);
        return $query->result_array();
    }

	public function getById($id){
		$query = $this->db->get_where($this->tableName, array($this->primaryKey => $id));
        return $query->row();
	}

    public function getByIdAsArray($id){
        $query = $this->db->get_where($this->tableName, array($this->primaryKey => $id));
        return $query->row_array();
    }

    public function getAllBy($field, $value){
        $query = $this->db->get_where($this->tableName, array($field => $value));
        return $query->result();
    }

    public function getAllByAsArray($field, $value){
        $query = $this->db->get_where($this->tableName, array($field => $value));
        return $query->result_array();
    }

	private function insert(){
    	$insert = $this->db->insert($this->tableName, $this->record);
        $this->record[$this->primaryKey] = $this->db->insert_id();
        return $insert;
    }

    private function update(){
        foreach ($this->record as $field => $value) {
        	$this->db->set($field, $value);
        }
        $this->db->where($this->primaryKey, $this->record[$this->primaryKey]);
        return $this->db->update($this->tableName);
    }

    public function delete(){
        $this->db->where($this->primaryKey, $this->record[$this->primaryKey]);
        return $this->db->delete($this->tableName);
    }

    public function deleteBy($field){
        $this->db->where($field, $this->record[$field]);
        return $this->db->delete($this->tableName);
    }

    public function save(){
        if (isset($this->record[$this->primaryKey]) && $this->record[$this->primaryKey] != null) { 
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    /**
     * Permits you to determine the number of rows in a particular table
     *
     */
    public function count(){
        return $this->db->count_all($this->tableName);
    }

    /* Functions of ArrayAccess Interface */
    public function offsetExists($offset){
    	return isset($this->record[$offset]);
    }
    
    public function offsetUnset($offset) {
        unset($this->record[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->record[$offset]) ? $this->record[$offset] : null;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->record[] = $value;
        } else {
            $this->record[$offset] = $value;
        }
    }
}