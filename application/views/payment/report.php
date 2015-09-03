<div class = "row mg_bottom_xs">
	<div class = "col-xs-12 page_title">Payment Report</div>
</div>

<form name = "payment_report_form" id = "payment_report_form" method = "post" action = "<?php echo site_url('payments/report'); ?>">
	<input type = "hidden" name = "hdn_user_id" id = "hdn_user_id" value = "<?php echo ($loggedUser === false ? '0' : $loggedUser['id']); ?>" />
	
	<div class = "row border pd_xs" style = "font-size:10pt;">
		<div class = "col-xs-12 col-md-2 center bold">Work Start Date</div>
		<div class = "col-xs-12 col-md-2">
			<input type = "text" name = "work_start_date" id = "work_start_date" value = "<?php echo set_value('work_start_date', ''); ?>" class = "date_picker bold" />
		</div>
		<div class = "col-xs-12 col-md-2 center bold">Work End Date</div>
		<div class = "col-xs-12 col-md-2">
			<input type = "text" name = "work_end_date" id = "work_end_date" value = "<?php echo set_value('work_end_date', ''); ?>" class = "date_picker bold" />
		</div>
		<div class = "col-xs-12 col-md-2 center bold">Payment Received</div>
		<div class = "col-xs-12 col-md-2">
			<input type = "text" name = "payment_received_date" id = "payment_received_date" value = "<?php echo set_value('payment_received_date', ''); ?>" class = "date_picker bold" />
		</div>
		<div class = "col-xs-12">
			<input type = "submit" class = "btn btn-success" name = "payment_report_form_submit" id = "payment_report_form_submit" value = "Generate Report" />
		</div>
	</div>
</form>

<?php if(isset($reportResult)) : ?>
	<?php if(is_array($reportResult) && count($reportResult) > 0) : ?>
		<?php 
			foreach($reportResult as $key => $value)
			{
				echo '<div class = "row">';
				echo '<div class = "row mg_bottom_xs border" style = "font-size:10pt; color:#286e90;">';
				echo '<div class = "col-xs-4">Name: '.$value['first_name'].' '.$value['last_name'].'</div>';
				echo '<div class = "col-xs-4">Email: '.$value['email'].'</div>';
				echo '<div class = "col-xs-4">Phone: '.$value['phone'].'</div>';
				echo '</div>';
				
				echo '<div class = "row border" style = "background-color:#336699; color:white; font-size:11pt;">
						<div class = "col-xs-2">Start Date</div>
						<div class = "col-xs-2">End Date</div>
						<div class = "col-xs-2">Received Date</div>
						<div class = "col-xs-2">Amount</div>
						<div class = "col-xs-4">Payment File(s)</div>
				</div>';
				
				if(isset($value['payments']) && count($value['payments']) > 0)
				{
					foreach($value['payments'] as $k => $v)
					{
						echo '<div class = "row border">';
						echo '<div class = "col-xs-2">'.convertDate($v['work_start_date'], 'Y-m-d', 'd/m/Y').'</div>';
						echo '<div class = "col-xs-2">'.convertDate($v['work_end_date'], 'Y-m-d', 'd/m/Y').'</div>';
						echo '<div class = "col-xs-2">'.$v['payment_received_date'].'</div>';
						echo '<div class = "col-xs-2">$'.$v['amount'].'</div>';
						echo '<div class = "col-xs-4">';
						
						if(isset($v['payment_files']) && count($v['payment_files']) > 0)
						{
							foreach($v['payment_files'] as $pfId => $pfValue)
								echo '<a href = "'.base_url($pfValue['full_path']).'" target = "_blank">'.$pfValue['file_name'].'</a><br/>';
						}
						else
							echo 'N/A';
							
						echo '</div>';						
						
						
						echo '</div>';
					}
				}
				
				echo '</div>';
			}
		?>
	<?php else : ?>
		<h4>No Payment Found</h4>
	<?php endif; ?>
<?php endif; ?>


