<?php

class User extends MY_Model
{
	public function __construct()
	{
		$this->_tableName = 'user';
		parent::__construct();
	}
	
	/**
	 * This function validates any input by checking if it is FALSE / EMPTY STRING 
	 * @param String/Int $input
	 * 
	 * @return boolean|string/int
	 */
	protected function _validateInput($input)
	{
		if($input === false || ($input = trim($input)) === '')
			return false;
		
		return $input;
	}
	
	public function getUserById($id)
	{
		if(($id = $this->_validateInput($id)) === false)
			throw new Exception('An Id must be specified to find a User');
		
		return $this->get($id);
	}
	
	/**
	 * This function validetes user login by checking the username against password provided
	 * 
	 * @param string $username
	 * @param string $password
	 * @throws Exception		-- if any exception happens
	 * 
	 * @return 
	 * 		FALSE 					--- if no user found
	 * 		Object/Action Record	--- If user is found  	 
	 */
	public function validateLogin($username = false, $password = false)
	{
		if(($username = $this->_validateInput($username)) === false)
			throw new Exception('Username must be provided to validate login');
		
		if(($password = $this->_validateInput($password)) === false)
			throw new Exception('Password must be provided to validate login');

		$output = false;

		$this->db->select('u.id as user_id, u.username as user_username, u.first_name as user_first_name, u.last_name as user_last_name, 
						   u.email as user_email, u.abn as user_abn, u.mobile as user_mobile, r.id as role_id, r.name as role_name, r.priority as role_priority')
				 ->from('user as u')
				 ->join('role as r', 'r.id = u.role_id and r.active = 1', 'inner')	
			     ->where(array('u.username' => $username))
			     //->or_where(array('u.email' => $username))
			     ->where(array('u.password' => sha1($password)))
			     ->limit(1);
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
			$output = $query->row();
		
		return $output;
	}
}