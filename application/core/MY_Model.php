<?php

class MY_Model extends CI_Model
{
	protected $_tableName = '';
	
	public function __construct()
	{
		parent::__construct();
		
		//if(!property_exists($this, '_tableName') || trim($this->_tableName) === '')
		//	throw new Exception('A Table name must be specified for the Model');
		
		$this->_tableName = trim($this->_tableName);
	}
	
	protected function _validateInput($input)
	{
		if($input === false || ($input = trim($input)) === '')
			return false;
		
		return $input;
	}
	
	private function _checkTableNameProperty()
	{
		if($this->_tableName === '')
			throw new Exception("A Table name must be specified for the Model");
		
		return true;
	}
	
	protected function _getTableName()
	{
		$this->_checkTableNameProperty();
		return $this->_tableName;
	}
	
	protected function _getFormattedTableName()
	{
		$this->_checkTableNameProperty(); 
		
		$tmp = array();
		$tableNameArray = explode('_', $this->_tableName);
		foreach($tableNameArray as $tnPart)
			$tmp[] = ucfirst(strtolower($tnPart));
			
		return implode(" ", $tmp);	
	}
	
	public function get($id)
	{
		$this->_checkTableNameProperty();
		
		if($id === false || ($id = trim($id)) === '')
			throw new Exception("Id must be provided to find an entry");
		
		$this->db->from($this->_tableName)
				 ->where(array('id' => $id))
				 ->limit(1);
		$query = $this->db->get();
		
		return (($query->num_rows() > 0) ? $query->row() : false); 
	}
}