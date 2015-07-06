<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		if(($user = checkLoggedUser()) !== false && ($role = getSessionData('role')) !== false)
		{
			$info = array('src' => '', 'data' => '');
			
			if(checkAdminRole($role, false))
			{
				$info['source'] = 'admin/index';
				$info['data'] = array();
			}	
			else if(checkContractorRole($role))
			{
				$this->load->model('report_type');
				$data = array();
				$data['rcData'] = $this->report_type->getReportCounters(array('rc.working_date' => date('Y-m-d'), 'rc.created_by_id' => $user['id']), array('rc.report_type_id' => 'ASC'), true);
				$data['user'] = $user;
				$data['role'] = $role;
				$data['workingDate'] = date('Y-m-d');
				
				$data['reportTypeArray'] = $this->report_type->getReportTypes();
				$info['source'] = 'report_counter';
				$info['data'] = $data;
			}
			else
			{	
				$info['source'] = 'admin/index';
				$info['data'] = array();
			}		
				
			$this->load->view('smart_view', $info);			
		}
		else
			$this->login();
	}
	
	public function login()
	{
		if(($user = checkLoggedUser()) !== false && ($role = getSessionData('role')) !== false)
			redirect('');
		else
		{	
			$info = array('pageTitle' => ' :: Reporter :: - Login', 'header' => false, 'footer' => false, 
						  'source' => 'user/login', 'data' => array());
			
			$this->load->view('smart_view', $info);
		}	
	}
	
	public function tester()
	{
		$this->load->model('user');
		try 
		{
			inspect($this->user->getUserById(1));
			die();
		}
		catch (Exception $ex)
		{
			var_dump($ex->getMessage()); die();	
		}	
		
		echo "This is your test page";
		//destroySession(); 
		inspect($_SESSION);
	}
}