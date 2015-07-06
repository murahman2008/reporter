<?php

class Report_Type extends MY_Model
{
	public function __construct()
	{
		$this->_tableName = 'report_type';
		parent::__construct();
	}
	
	public function getReportTypeById($id = false)
	{
		if($id === false || ($id = trim($id)) === '')
			throw new Exception("Id must be provided to find a Report Type Entry");
		
		return $this->get($id);
	}
	
	/**
	 * This function retuns a set of report type (s) based on the criteria provided 
	 * 
	 * @param Array $option
	 * @param Array $orderBy
	 * 
	 * @return Array of Active Records (ojects)
	 */
	public function getReportTypes(Array $option = array(), Array $orderBy = array())
	{
		//if(count($option) <= 0)
		//	throw new Exception('A set of options must be specified to find Report Type(s)');
		
		$output = array();
		
		foreach($option as $key => $value)
		{
			if(is_array($value))
			{
				if(count($value) > 0)
					$this->db->where_in($key, $value);
			}	
			else
				$this->db->where(array($key => $value));
		}
		
		if(count($orderBy) > 0)
		{
			foreach($orderBy as $key => $value)
			{
				if(in_array(strtolower(trim($value)), array('asc', 'desc')))
					$this->db->order_by($key, $value);
				else
					continue;
			}
		}
		
		$this->db->from('report_type')
				 ->where(array('active' => 1));	
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
				$output[$row->id] = $row;		
		}
				 
		return $output;			
	}
	
	private function _validateInputForAddUpdateCounterForReportType(&$reportTypeId, &$counter, &$operator, &$userId, &$workingDate)
	{
		if(($reportTypeId = $this->_validateInput($reportTypeId)) === false)
			throw new Exception('Report Type must be specified to add/update counter');
		
		if(($counter = $this->_validateInput($counter)) === false)
			throw new Exception('Counter must be specified to add/update counter');
		else
		{
			if(!is_numeric($counter) || $counter <= 0)
				throw new Exception('Valid Counter value must be specified to add/update counter');
		}
		
		if(($operator = $this->_validateInput($operator)) === false)
			throw new Exception('Operator must be specified to add/update counter');
		else
		{
			if(!in_array($operator, array('+', '-')))
				throw new Exception('A valid operator must be provided. Operatro value can be + / -');
		}
		
		if(($userId = $this->_validateInput($userId)) === false)
			throw new Exception('User Info must be specified to add/update counter');
		
		if(($workingDate = $this->_validateInput($workingDate)) === false)
			throw new Exception('Working Date must be specified to add/update counter');
		else
			$workingDate = date('Y-m-d', strtotime($workingDate));
		
		return true;
	}
	
	private function _finalizeCounter($previousCounter, $newCounter, $operator)
	{
		$finalCounter = $previousCounter;
		
		if($operator == '-')
		{
			$finalCounter = $previousCounter - $newCounter;
			if($finalCounter <= 0)
				$finalCounter = 0;
		}
		else if($operator == '+')
			$finalCounter = $previousCounter + $newCounter;

		return $finalCounter;
	}
	
	public function addUpdateCounterForReportType($reportTypeId, $counter, $operator, $userId, $workingDate)
	{
		$this->_validateInputForAddUpdateCounterForReportType($reportTypeId, $counter, $operator, $userId, $workingDate);
		
		$rcId = false;
		$existingRcArray = $this->getReportCounters(array('rc.report_type_id' => $reportTypeId, 'rc.created_by_id' => $userId, 'working_date' => $workingDate), array(), false);
		if(count($existingRcArray) > 0)
		{	
			$existingRc = $existingRcArray[0];
			$rcId = $existingRc->report_counter_id;
			
			$newCounter = $this->_finalizeCounter($existingRc->report_counter_counter, $counter, $operator);
			$updateSuccess = $this->updateReportCounter(array('id' => $existingRc->report_counter_id, 'report_type_id' => $reportTypeId, 'working_date' => $workingDate, 'created_by_id' => $userId), array('counter' => $newCounter));
			if(!$updateSuccess)
				throw new Exception('Report Counter Update Failed');
		}
		else
		{
			$rcId = $this->addReportCounter(array('report_type_id' => $reportTypeId, 'counter' => $counter, 'created_by_id' => $userId, 'working_date' => $workingDate));
			if($rcId === false)
				throw new Exception('Report Counter Add Failed');
		}
		
		$rcArray = $this->getReportCounters(array('rc.id' => $rcId), array(), false);
		$reportCounter = $rcArray[0];
		
		return $reportCounter;
	}
	
	public function addReportCounter(Array $insertCriteria)
	{
		if(count($insertCriteria) <= 0)
			throw new Exception('Nothing to insert for Report Counter');
		
		if(!isset($inserCriteria['active']))
			$insertCriteria['active'] = 1;
		
		if(!isset($inserCriteria['created']))
			$insertCriteria['created'] = date('Y-m-d H:i:s');
		
		if(!isset($inserCriteria['updated']))
			$insertCriteria['updated'] = date('Y-m-d H:i:s');
		
		if(!isset($inserCriteria['created_by_id']))
			$insertCriteria['created_by_id'] = (($loggedUser = checkLoggedUser()) !== false ? $loggedUser['id'] : 0);
		
		if(!isset($inserCriteria['updated_by_id']))
			$insertCriteria['updated_by_id'] = (($loggedUser = checkLoggedUser()) !== false ? $loggedUser['id'] : 0);
		
		$result = $this->db->insert('report_counter', $insertCriteria);
		
		if($result)
			return $this->db->insert_id();
		else
			return false;
	}
	
	public function updateReportCounter(Array $condition, Array $updateCriteria)
	{
		if(count($condition) <= 0 || count($updateCriteria) <= 0)
			throw new Exception('Update criteria & condition must be provided to update report counter');
		
		if(!isset($updateCriteria['updated']))
			$updateCriteria['updated'] = date('Y-m-d H:i:s');
		if(!isset($updateCriteria['updated_by_id']))
			$updateCriteria['updated_by_id'] = (($loggedUser = checkLoggedUser()) !== false ? $loggedUser['id'] : 0);
		
		$this->db->where($condition);
		$result = $this->db->update('report_counter', $updateCriteria);
		
		return $result;
	}
	
	/**
	 * 
	 * @param array $option
	 * @param array $orderBy
	 * @return multitype:unknown
	 */
	public function getReportCounters(Array $option = array(), Array $orderBy = array(), $formatOutput = false)
	{
		$output = array();
		
		foreach($option as $key => $value)
		{
			if(is_array($value))
			{
				if(count($value) > 0)
					$this->db->where_in($key, $value);
			}
			else
				$this->db->where(array($key => $value));
		}
		
		if(count($orderBy) > 0)
		{
			foreach($orderBy as $key => $value)
			{
				if(in_array(strtolower(trim($value)), array('asc', 'desc')))
					$this->db->order_by($key, $value);
				else
					continue;
			}
		}
		else
		{	
			$this->db->order_by('rc.created_by_id', 'ASC');
			$this->db->order_by('rc.report_type_id', 'ASC');
		}	
		
		$this->db->select('rc.id as report_counter_id, rc.report_type_id as report_type_id, rt.name as report_type_name, rc.counter as report_counter_counter, rc.created as report_counter_created,
						   rc.working_date as report_counter_working_date, u.id as user_id, u.username as user_username, u.first_name as user_first_name, u.last_name as user_last_name,
						   u.email as user_email, u.role_id as role_id, r.name as role_name, r.priority as role_priority')
				 ->from('report_counter as rc')
				 ->join('report_type rt', 'rt.id = rc.report_type_id and rt.active = 1', 'inner')
				 ->join('user as u', 'u.id = rc.created_by_id and u.active = 1', 'inner')
				 ->join('role as r', 'r.id = u.role_id and r.active = 1', 'inner');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			if(!$formatOutput)
			{
				foreach($query->result() as $row)
					$output[] = $row;
			}
			else
			{
				foreach($query->result() as $row)
				{
					if(!isset($output[$row->user_id]))
					{	
						$output[$row->user_id] = array();
						$output[$row->user_id]['id'] = trim($row->user_id);
						$output[$row->user_id]['username'] = trim($row->user_username);
						$output[$row->user_id]['first_name'] = trim($row->user_first_name);
						$output[$row->user_id]['last_name'] = trim($row->user_last_name);
						$output[$row->user_id]['email'] = trim($row->user_email);
						$output[$row->user_id]['role_id'] = trim($row->role_id);
						$output[$row->user_id]['role_name'] = trim($row->role_name);
						$output[$row->user_id]['role_priority'] = trim($row->role_priority);
						$output[$row->user_id]['report_counters'] = array();
					}
					
					if(!isset($output[$row->user_id]['report_counters'][$row->report_counter_working_date]))
						$output[$row->user_id]['report_counters'][$row->report_counter_working_date] = array();
						
					if(!isset($output[$row->user_id]['report_counters'][$row->report_counter_working_date][$row->report_type_id]))
					{
						$output[$row->user_id]['report_counters'][$row->report_counter_working_date][$row->report_type_id] = array();
						$output[$row->user_id]['report_counters'][$row->report_counter_working_date][$row->report_type_id]['id'] = trim($row->report_type_id);
						$output[$row->user_id]['report_counters'][$row->report_counter_working_date][$row->report_type_id]['name'] = trim($row->report_type_name);
						$output[$row->user_id]['report_counters'][$row->report_counter_working_date][$row->report_type_id]['rc_id'] = trim($row->report_counter_id);
						$output[$row->user_id]['report_counters'][$row->report_counter_working_date][$row->report_type_id]['rc_date'] = trim($row->report_counter_working_date);
						$output[$row->user_id]['report_counters'][$row->report_counter_working_date][$row->report_type_id]['rc_counter'] = trim($row->report_counter_counter);
					}
				}
			}	
		}
		
		return $output;
	}
}