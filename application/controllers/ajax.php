<?php

class Ajax extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		if(!$this->input->is_ajax_request())
			redirect('');

		$this->load->model('user');
	}
	
	public function update_report_counter()
	{
		$output = array();
		
		try 
		{
			if(isset($_POST['reportTypeId']) && ($reportTypeId = trim($_POST['reportTypeId'])) !== '' && 
			   isset($_POST['workingDate']) && ($workingDate = trim($_POST['workingDate'])) !== '' && 
			   isset($_POST['userId']) && ($userId = trim($_POST['userId'])) !== '' && 
			   isset($_POST['counter']) && ($counter = trim($_POST['counter'])) !== '' && $counter > 0 && 
			   isset($_POST['operator']) && ($operator = trim($_POST['operator'])) !== '' && in_array($operator, array('+', '-'))) 
			{
				$this->load->model('report_type');
				$reportCounter = $this->report_type->addUpdateCounterForReportType($reportTypeId, $counter, $operator, $userId, $workingDate);
				
				$output['status'] = true;
				$output['data'] = $reportCounter;
			}
			else
				throw new Exception('Incomplete / Invalid data provided. Unable to update counter');	
		}
		catch(Exception $ex)
		{
			$output['status'] = false;
			$output['message'] = $ex->getMessage();
		}

		echo json_encode($output);
	}
	
	public function login()
	{
		$output = array();
		try 
		{
			if(isset($_POST['username']) && ($username = trim($_POST['username'])) !== '' && isset($_POST['password']) && ($password = trim($_POST['password'])) !== '')
			{
				if(($loggedUser = $this->user->validateLogin($username, $password)) !== false)
				{					
					$userData = array('id' => $loggedUser->user_id, 'username' => $loggedUser->user_username, 'first_name' => $loggedUser->user_first_name, 
									  'last_name' => $loggedUser->user_last_name, 'email' => $loggedUser->user_email, 'mobile' => $loggedUser->user_mobile, 
									  'abn' => $loggedUser->user_abn);
					setSessionData('user', $userData);
					
					$roleData = array('id' => $loggedUser->role_id, 'name' => $loggedUser->role_name, 'priority' => $loggedUser->role_priority);
					setSessionData('role', $roleData);
					
					$output['status'] = true;
					$output['message'] = "Login Successful";
				}
				else 
					throw new Exception('Invalid Username and / or Password. Login Failed...');	
			}
			else
				throw new Exception('Both username and password must be provided');
		}
		catch(Exception $ex)
		{
			$output['status'] = false;
			$output['message'] = $ex->getMessage();	
		}

		echo json_encode($output);
	}

	public function calculate_shipping()
	{
		$output = array();
		$data = '';
		
		try 
		{
			if(isset($_POST['postcode_id']) && ($postcodeId = trim($_POST['postcode_id'])) !== '' && 
			   isset($_POST['shipping_code']) && ($shippingCode = trim($_POST['shipping_code'])) !== '')
			{
				$query = $this->db->query("select * from `wp_bubs_shipping_rates` where `postcode_id` = ".$postcodeId." and `shipping_code` = '".$shippingCode."'");
				if($query->num_rows() > 0)
				{
					$row = $query->row();
					$data = "Your shipping cost is ".$row->cost;
				}
				else
					$data = "<b>Sorry We cannot calcualte shipping cost for this postcode and product. Please call us to confirm your shipping cost</b>";
				
				$output['status'] = true;
				$output['data'] = $data;
			}
			else
				throw new Exception('Unable to calcualte shipping as postcode and shipping code are not provided');	
		}
		catch(Exception $ex)
		{
			$output['status'] = false;
			$output['message'] = $ex->getMessage();
		}	
		
		echo json_encode($output);
	}
	
	public function shipping()
	{
		$output = array();
		
		try 
		{
			if(isset($_GET['term']) && ($term = trim($_GET['term'])) !== '')
			{
				$query = $this->db->query("select * from `wp_bubs_postcode_list` where 1 AND (postcode like '%".$term."%' OR suburb like '%".$term."%')");
				if($query->num_rows() > 0)
				{
					foreach($query->result() as $row)
					{
						$label = $row->postcode.' '.$row->suburb.', '.$row->state;
						$output[] = array('id' => $row->id, 'value' => $label, 'label' => $label);
					}	
				}
				else 
					$output[] = array('id' => 0, 'value' => 'Nothing Found', 'label' => 'Nothing found');
			}	
			else
				throw new Exception('No search term specified');
		}
		catch(Exception $ex)
		{
			$output = array('id' => 0, 'value' => $ex->getMessage(), 'label' => $ex->getMessage());
		}

		echo json_encode($output);
	}
	
}