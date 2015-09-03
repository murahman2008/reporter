<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('report_type');
	}
	
	/**
	 * 
	 * @throws Exception
	 */
	public function date_range_report()
	{
		if(($user = checkLoggedUser()) === false || ($role = getSessionData('role')) === false)
			redirect('login');
		
		$selectedUser = ((checkContractorRole($role) == true) ? $user : false); 
		$reportTypeArray = $this->report_type->getReportTypes();

		$dataParam = array('selectedUser' => $selectedUser, 'reportTypeArray' => $reportTypeArray);
		
		try 
		{
			if(isset($_POST['date_range_report_submit']))
			{
				$this->form_validation->set_rules('report_start_date', 'Report Start Date', 'trim|required');
				$this->form_validation->set_rules('report_end_date', 'Report End Date', 'trim|required');
				$this->form_validation->set_rules('report_type', 'Report Type', 'trim|required|numeric');
				$this->form_validation->set_rules('selected_user', 'Selected User', 'trim|required');
				
				if($this->form_validation->run() !== false)
				{
					$_POST['report_start_date'] = convertDate($_POST['report_start_date'], 'd/m/Y', 'Y-m-d');
					$_POST['report_end_date'] = convertDate($_POST['report_end_date'], 'd/m/Y', 'Y-m-d');
					
					$output = $this->report_type->generateDateRangeReport($_POST);
					$dataParam['reportResult'] = $output;
				}
				else
					throw new Exception(validation_errors());
			}		
		}
		catch(Exception $ex)
		{
			setSessionData('view_error_msg', $ex->getMessage());
		}		
		
		$viewParam = array('header' => true, 'footer' => true, 'source' => 'report/date_range_report', 'data' => $dataParam);
		$this->load->view('smart_view', $viewParam);
	}
}