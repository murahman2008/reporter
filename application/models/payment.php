<?php

class Payment extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	private function _checkForEmptyInput($input)
	{
		if(is_array($input))
		{
			if(count($input) <= 0)
				return false;
			else
				return $input;
		}
		else if(is_object($input))
		{
			if(count((array)$input) <= 0)
				return false;
			else
				return $input;
		}	
		else if(trim($input) === '')
			return false;
		else
			return trim($input);
		
		return true;
	}
	
	/**
	 * This function finds a Payment by id
	 * It can return an associate array containing the payment as well as all the payment files related to it in an organised manner
	 * 
	 * @param int $id
	 * @param Boolean $details
	 * 
	 * @throws Exception - if any error occurs
	 * @return Array
	 */
	public function getPaymentById($id, $details = false)
	{
		if(($id = $this->_checkForEmptyInput($id)) === false)
			throw new Exception('Payment Id Must Be Provided To Find Payment');	
		
		$output = array();
		
		if($details === false)
		{
			$this->db->from('payments')
					 ->where(array('id' => $id))
					 ->limit(1);
			$query = $this->db->get();
			$output = (($query->num_rows() > 0) ? $query->result() : false);
		}
		else
		{
			$this->db->select('p.id as payment_id, p.work_start_date as payment_work_start_date, p.work_end_date as payment_work_end_date, p.active as payment_active, 
							   p.created as payment_created, p.updated as payment_updated, p.amount as payment_amount, p.payment_received_date as payment_received_date, 
							   p.payment_received_date as paymetn_received_date, p.created_by_id as payment_created_by_id, p.updated_by_id as payment_updated_by_id,
							   pf.id as pf_id, pf.file_name as pf_file_name, pf.extension as pf_extension, pf.full_path as pf_full_path, pf.created as pf_created, pf.updated as pf_updated')
					 ->from('payments as p')
					 ->join('payment_files as pf', 'pf.payment_id = p.id and pf.active = 1', 'left')
					 ->where(array('p.id' => $id))
					 ->order_by('p.created', 'DESC');
			$query = $this->db->get();
			if($query->num_rows() > 0)
			{
				foreach($query->result() as $row)
				{
					if(!isset($output[$row->payment_id]))
					{	
						$output[$row->payment_id] = array();
						$output[$row->payment_id]['id'] = trim($row->payment_id);
						$output[$row->payment_id]['work_start_date'] = trim($row->payment_work_start_date);
						$output[$row->payment_id]['work_end_date'] = trim($row->payment_work_end_date);
						$output[$row->payment_id]['amount'] = trim($row->payment_amount);
						$output[$row->payment_id]['payment_received_date'] = trim($row->payment_received_date);
						$output[$row->payment_id]['active'] = trim($row->payment_active);
						$output[$row->payment_id]['created'] = trim($row->payment_created);
						$output[$row->payment_id]['created_by_id'] = trim($row->payment_created_by_id);
						$output[$row->payment_id]['updated'] = trim($row->payment_updated);
						$output[$row->payment_id]['updated_by_id'] = trim($row->payment_updated_by_id);
						$output[$row->payment_id]['payment_files'] = array();
					}

					if(trim($row->pf_id) !== '')
					{
						$output[$row->payment_id]['payment_files'][$row->pf_id] = array();
						$output[$row->payment_id]['payment_files'][$row->pf_id]['id'] = trim($row->pf_id);
						$output[$row->payment_id]['payment_files'][$row->pf_id]['file_name'] = trim($row->pf_file_name);
						$output[$row->payment_id]['payment_files'][$row->pf_id]['extension'] = trim($row->pf_extension);
						$output[$row->payment_id]['payment_files'][$row->pf_id]['full_path'] = trim($row->pf_full_path);
					}	
				}	
			}
		}

		return $output;
	}
	
	public function getPayments(Array $option = array())
	{
		if(count($option) > 0)
		{
						
		}
	}
	
	private function _checkDuplicatePayment($workStartDate, $workEndDate)
	{
		$output = array();
		
		$this->db->from('payments')
				 ->where(array('active' =>  1, 'work_start_date' => $workStartDate, 'work_end_date' => $workEndDate));
		$query = $this->db->get();
		if($query->num_rows() <= 0)
		{	
			$query = $this->db->query("select * from payments 
							  		   where 1 AND ((work_start_date <= ? AND work_end_date >= ?) OR (work_start_date <= ? AND work_end_date >= ?))
							  		   order by work_end_date DESC", array($workStartDate, $workStartDate, $workEndDate, $workEndDate));
			
			if($query->num_rows() > 0)
			{
				foreach($query->result() as $row)
					$output[] = $row;
			}
		}	
		return $output;
	}
	
	public function addPayment(Array $data, Array $fileArray)
	{
		/// first check if the payment data is provided ///
		if(count($data) <= 0)
			throw new Exception('No Data provided. Failed to add new payment');
		
		if(!isset($data['work_start_date']) || ($workStartDate = trim($data['work_start_date'])) === '')
			throw new Exception('A Start Date must be specified to add a new payment');
		
		if(!isset($data['work_end_date']) || ($workEndDate = trim($data['work_end_date'])) === '')
			throw new Exception('An End Date must be specified to add a new payment');
		
		if(!isset($data['amount']) || ($amount = trim($data['amount'])) === '')
			throw new Exception('An Amount must be specified to add a new payment');
		
		if(!is_numeric($amount) || $amount <= 0)
			throw new Exception('Amount must be numeric and greater than Zero');
		
		$paymentReceivedDate = '';
		if(isset($data['payment_received_date']) && ($paymentReceivedDate = trim($data['payment_received_date'])) !== '')
		{
			$d = DateTime::createFromFormat('d/m/Y', $paymentReceivedDate);
			if(!$d || $d->format('d/m/Y') != $paymentReceivedDate)
				throw new Exception('Payment Received Date must be a valid date');
			else
				$paymentReceivedDate = $d->format('Y-m-d');
		}
		
		/// now check if the payment file data is provided ///
		if(count($fileArray) <= 0 || !isset($fileArray['name']) || !is_array($fileArray['name']) || count($fileArray['name']) <= 0 || 
		   !isset($fileArray['tmp_name']) || !is_array($fileArray['tmp_name']) || count($fileArray['tmp_name']) <= 0)
		{
			throw new Exception('One or more payment reference file(s) must be provided to create new payment');
		}	
		
		$workStartDate = DateTime::createFromFormat('d/m/Y', $workStartDate)->format('Y-m-d');
		$workEndDate = DateTime::createFromFormat('d/m/Y', $workEndDate)->format('Y-m-d');
		
		if(strtotime($workStartDate) >= strtotime($workEndDate))
			throw new Exception('Work End Date Must Be Greater Than Work Start Date');
		
		$duplicatePaymentArray = $this->_checkDuplicatePayment($workStartDate, $workEndDate);
		if(count($duplicatePaymentArray) > 0)
		{
			$tmp = array();
			foreach($duplicatePaymentArray as $dp)
			{
				$tmp[] = "<a target = '_blank' href = '".site_url('payments/view/'.$dp->id)."'>A Duplicate Payment Found In System With Date Range 
							from [".$dp->work_start_date." - ".$dp->work_end_date."]. 
							Payment Amount is [".$dp->amount."] Received @ [".$dp->payment_received_date."]</a>";
			}
			
			throw new Exception(implode("<br/>", $tmp));
		}
		
		$uploadedFileArray = $pfArray = array();
		try 
		{
			$this->db->trans_begin();
			
			$user = checkLoggedUser();
			
			$paymentId = false;
			
			$this->db->from('payments')
					 ->where(array('work_start_date' => $workStartDate, 'work_end_date' => $workEndDate, 'created_by_id' => $user['id']))
					 ->order_by('updated', 'DESC');
			$query = $this->db->get();
			if($query->num_rows() > 0)
			{
				$dfArray = $dfIdArray = array();
				$counter = 0;
				
				foreach($query->result() as $row)
				{
					if($counter === 0)
						$paymentId = $row->id;
					else
						$this->db->delete('payments', array('id' => $row->id));
					
					$this->db->from('payment_files')
							 ->where(array('payment_id' => $row->id));
					
					$query2 = $this->db->get();
					
					if($query2->num_rows() > 0)
					{
						foreach($query2->result() as $row2)
						{
							$dfArray[] = $row2->full_path;
							$dfIdArray[] = $row2->id;
						}
					}
					$counter++;
				}
				
				if(count($dfArray) > 0)
				{
					foreach($dfArray as $df)
					{
						if(file_exists(FCPATH.$df))
							unlink(FCPATH.$df);
					}	
				}
				
				if(count($dfIdArray) > 0)
				{
					$this->db->where_in('id', $dfIdArray)
							 ->delete('payment_files');
				}
				
				$this->db->where(array('id' => $paymentId))
						 ->update('payments', array('amount' => $amount, 'active' => 1, 'updated' => date('Y-m-d H:i:s'), 'updated_by_id' => $user['id']));
			}
			else
			{
				$this->db->insert('payments', array('work_start_date' => $workStartDate, 'work_end_date' => $workEndDate, 
													'amount' => ($amount*1), 'payment_received_date' => $paymentReceivedDate, 'active' => 1,
													'created' => date('Y-m-d H:i:s'), 'updated' => date('Y-m-d H:i:s'),
													'created_by_id' => $user['id'], 'updated_by_id' => $user['id']));
				$paymentId = $this->db->insert_id();
			}

			if($paymentId === false)
				throw new Exception('Unable to create / update payment');
			
			$fullPath = FCPATH.'assets/'.$user['username'].'/'.str_replace("-", "_", $workStartDate)."to".str_replace("-", "_", $workEndDate);
			
			if(!file_exists(FCPATH.'assets/'.$user['username']))
				@mkdir(FCPATH.'assets/'.$user['username']);
			if(!file_exists($fullPath))
				@mkdir($fullPath);
			
			$uploadError = false;
			
			foreach($fileArray['name'] as $index => $fileName)
			{
				$fNameArray = explode(".", $fileName);
				$extension = trim(strtolower($fNameArray[count($fNameArray) - 1]));
				
				if(isset($fileArray['tmp_name'][$index]) && trim($fileArray['tmp_name'][$index]) !== '')
				{	
					if(!move_uploaded_file($fileArray['tmp_name'][$index], $fullPath.'/'.$fileName))
					{	
						$uploadError = true;
						break;
					}
					else
						$uploadedFileArray[] = $fullPath.'/'.$fileName;
				}
				else
				{	
					$uploadError = true;
					break;
				}			
				
				$pfArray[] = array('payment_id' => $paymentId, 'file_name' => $fileName, 'extension' => $extension, 
								   'full_path' => str_replace(FCPATH, '', $fullPath).'/'.$fileName, 'active' => 1, 
								   'created' => date('Y-m-d H:i:s'), 'updated' => date('Y-m-d H:i:s'), 'created_by_id' => $user['id'], 'updated_by_id' => $user['id']);	
			}
			
			if($uploadError == true || count($uploadedFileArray) !== count($pfArray))
				throw  new Exception('File upload failed. Please try again');

			if(count($pfArray) === 0 || count($uploadedFileArray) === 0)
				throw new Exception('No Payment Reference file(s) provided. Failed to add new payment');
			
			$this->db->insert_batch('payment_files', $pfArray);
			
			$this->db->trans_commit();
		}
		catch(Exception $ex)
		{
			$this->db->trans_rollback();
			
			foreach($uploadedFileArray as $uf)
				@unlink($uf);
			
			throw $ex;
		}
		
		return true;
	}

	public function paymentReport($startDate = false, $endDate = false, $userId = false)
	{
		if($startDate === false || ($startDate = trim($startDate)) === '')
			throw new Exception('Start Date is necessary to generate Payment Report');
		
		if($endDate === false || ($endDate = trim($endDate)) === '')
			throw new Exception('End Date is necessary to generate Payment Report');
		
		$startDate = convertDate($startDate, 'd/m/Y', 'Y-m-d');
		$endDate = convertDate($endDate, 'd/m/Y', 'Y-m-d');
		
		$output = array();
		
		$userId = trim($userId);
		if($userId == '0')
			$userId = '';
		
		$query = "select 
					p.id as payment_id, p.work_start_date as payment_work_start_date, p.work_end_date as payment_work_end_date, p.amount as payment_amount,
				  	p.payment_received_date as payment_received_date, p.created as payment_created, p.updated as payment_updated, 
					p.created_by_id as payment_created_by_id, u.first_name as user_first_name, u.last_name as user_last_name, u.email as user_email, u.phone as user_phone,
					pf.id as pf_id, pf.full_path as pf_full_path, pf.file_name as pf_file_name, pf.extension as pf_extension  
				  from payments p
				  left join payment_files pf ON (pf.payment_id = p.id and pf.active = 1)
				  inner join user u ON (u.id = p.created_by_id and u.active = 1)
				  inner join role r ON (r.id = u.role_id and r.active = 1)		 
				  where 
					((p.work_start_date <= ? and p.work_end_date >= ?) OR (p.work_start_date <= ? and p.work_end_date >= ?)) OR 
					((p.work_start_date >= ? AND p.work_start_date <= ?) OR (p.work_end_date >= ? AND p.work_end_date <= ?))";
		
		$queryParam = array($startDate, $startDate, $endDate, $endDate, $startDate, $endDate, $startDate, $endDate);
		
		if($userId !== '' && $userId !== '0')
		{	
			$query .= " and p.created_by_id = ?";	
			$queryParam[] = $userId;
		}

		$query .= " order by p.created_by_id ASC, p.work_start_date ASC";

		$query = $this->db->query($query, $queryParam);	
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				if(!isset($output[$row->payment_created_by_id]))
				{	
					$output[$row->payment_created_by_id] = array();
					$output[$row->payment_created_by_id]['id'] = trim($row->payment_created_by_id);
					$output[$row->payment_created_by_id]['first_name'] = trim($row->user_first_name);
					$output[$row->payment_created_by_id]['last_name'] = trim($row->user_last_name);
					$output[$row->payment_created_by_id]['email'] = trim($row->user_email);
					$output[$row->payment_created_by_id]['phone'] = trim($row->user_phone);
					$output[$row->payment_created_by_id]['payments'] = array();
				}
				
				if(!isset($output[$row->payment_created_by_id]['payments'][$row->payment_id]))
				{
					$output[$row->payment_created_by_id]['payments'][$row->payment_id] = array();
					$output[$row->payment_created_by_id]['payments'][$row->payment_id]['id'] = trim($row->payment_id);
					$output[$row->payment_created_by_id]['payments'][$row->payment_id]['work_start_date'] = trim($row->payment_work_start_date);
					$output[$row->payment_created_by_id]['payments'][$row->payment_id]['work_end_date'] = trim($row->payment_work_end_date);
					$output[$row->payment_created_by_id]['payments'][$row->payment_id]['amount'] = trim($row->payment_amount);
					$output[$row->payment_created_by_id]['payments'][$row->payment_id]['payment_received_date'] = trim($row->payment_received_date);
					$output[$row->payment_created_by_id]['payments'][$row->payment_id]['payment_received_date'] = trim($row->payment_received_date);
					$output[$row->payment_created_by_id]['payments'][$row->payment_id]['payment_files'] = array();
				}
				
				if(trim($row->pf_id) !== '')
				{	
					if(!isset($output[$row->payment_created_by_id]['payments'][$row->payment_id]['payment_files'][$row->pf_id]))
					{
						$output[$row->payment_created_by_id]['payments'][$row->payment_id]['payment_files'][$row->pf_id] = array();
						$output[$row->payment_created_by_id]['payments'][$row->payment_id]['payment_files'][$row->pf_id]['id'] = trim($row->pf_id);
						$output[$row->payment_created_by_id]['payments'][$row->payment_id]['payment_files'][$row->pf_id]['file_name'] = trim($row->pf_file_name);
						$output[$row->payment_created_by_id]['payments'][$row->payment_id]['payment_files'][$row->pf_id]['full_path'] = trim($row->pf_full_path);
						$output[$row->payment_created_by_id]['payments'][$row->payment_id]['payment_files'][$row->pf_id]['extension'] = trim($row->pf_extension);
					}		
				}			
			}	
		}

		return $output;
	}
}