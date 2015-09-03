<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payments extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('payment');
	}
	
	public function check_date($input)
	{
		$d = DateTime::createFromFormat('d/m/Y', $input);
		if($d && ($input == $d->format('d/m/Y')))
			return true;
		else
		{
			$this->form_validation->set_message('check_date', 'The %s is not a valid date');
			return false;
		}
	}
	
	private function _checkPaymentFilesForAdd(Array $fileArray)
	{
		if(!isset($fileArray['payment_file']) || !is_array($fileArray['payment_file']))
			throw new Exception('No Payment File Uploaded. Cannot Add New Paymnent');

		$fileArray = $fileArray['payment_file'];
		if(count($fileArray) <= 0 || !isset($fileArray['name']) || !isset($fileArray['tmp_name']))
			throw new Exception('No Payment File Uploaded. Cannot Add New Paymnent');
		
		if(!is_array($fileArray['name']))
			$nameArray = array($fileArray['name']);
		else
			$nameArray = $fileArray['name'];
		
		if(count($nameArray) <= 0)
			throw new Exception('No Payment File Uploaded. Cannot Add New Paymnent');
		
		return true;
	}
	
	public function add()
	{
		if(($user = checkLoggedUser()) !== false && ($role = getSessionData('role')) !== false)
		{
			try 
			{
				if(isset($_POST['submit_add_payment']))
				{
					$this->form_validation->set_rules('work_start_date', 'Work Start Date', 'trim|required|callback_check_date');
					$this->form_validation->set_rules('work_end_date', 'Work End Date', 'trim|required|callback_check_date');
					$this->form_validation->set_rules('payment_received_date', 'Payment Received Date', 'trim');
					$this->form_validation->set_rules('amount', 'Amount', 'trim|required');
					
					if($this->form_validation->run() !== false)
					{
						$this->_checkPaymentFilesForAdd($_FILES);
						
						//var_dump($_POST['amount']); die();
						
						$this->payment->addPayment($_POST, $_FILES['payment_file']);
						setSessionData('view_success_msg', "New Payment Added Successfully");
					}
					else
						throw new Exception(validation_errors());	
				}	
			}
			catch(Exception $ex)
			{
				setSessionData('view_error_msg', $ex->getMessage());	
			}	
			
			$this->load->view('smart_view', array('source' => 'payment/add', 'header' => true, 'footer' => true, 
												  'pageTitle' => 'Add New Payment', 'data' => array()));
		}
		else
			redirect('/login');	
	}
	
	public function report()
	{
		if(($user = checkLoggedUser()) === false || ($role = getSessionData('role')) === false)
			redirect('/login');
		
		$loggedUser = false;
		if(($isAdmin = checkAdminRole($role, false)) === false)
			$loggedUser = $user;
		
		$data = array('loggedUser' => $loggedUser);
		
		try 
		{
			if(isset($_POST['payment_report_form_submit']) && trim($_POST['payment_report_form_submit']) !== '')
			{
				$this->form_validation->set_rules('work_start_date', 'Work Start Date', 'trim|required|callback_check_date');
				$this->form_validation->set_rules('work_end_date', 'Work End Date', 'trim|required|callback_check_date');
				$this->form_validation->set_rules('payment_received_date', 'Payment Received Date', 'trim');
				$this->form_validation->set_rules('hdn_user_id', 'hdn_user_id', 'trim');
				
				if($this->form_validation->run() !== false)
				{
					$output = $this->payment->paymentReport($_POST['work_start_date'], $_POST['work_end_date'], $_POST['hdn_user_id']);
					$data['reportResult'] = $output;
				}
				else
					throw new Exception(validation_errors());
			}
		}
		catch(Exception $ex)
		{
			setSessionData('view_error_msg', $ex->getMessage());
		}		
		
		
		$viewParam = array('header' => true, 'footer' => true, 'source' => 'payment/report', 'data' => $data);
		
		$this->load->view('smart_view', $viewParam);
	}
	
	public function view()
	{
		if(($user = checkLoggedUser()) === false || ($role = getSessionData('role')) === false)
			redirect('/login');
		
		$paymentId = trim($this->uri->segment(3, 0));
		if($paymentId === '' || $paymentId == '0')
			redirect('payments/report');
		
		$payment = $this->payment->getPaymentById($paymentId, true);
			
		$this->load->view('smart_view', array('source' => 'payment/view', 'header' => true, 'footer' => true, 'data' => array('payment' => $payment)));	
		
	}

}
