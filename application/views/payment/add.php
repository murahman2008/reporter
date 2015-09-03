<div class = "row page_title">
	<div class = "col-xs-12">Add New Payment</div>
</div>

<form name = "add_payment_form" id = "add_payment_form" enctype="multipart/form-data" method = "post" action = "<?php echo site_url('payments/add'); ?>">
	<div class = "row pd_top_xs pd_bottom_xs border">
		<div class = "col-xs-12 col-md-3">Start Date</div>
		<div class = "col-xs-12 col-md-3">
			<input type = "text" name = "work_start_date" id = "work_start_date" class = "date_picker" value = "<?php echo set_value('work_start_date'); ?>" readonly = "readonly" />
		</div>
		<div class = "col-xs-12 col-md-3">End Date</div>
		<div class = "col-xs-12 col-md-3">
			<input type = "text" name = "work_end_date" id = "work_end_date" class = "date_picker" value = "<?php echo set_value('work_end_date'); ?>" readonly = "readonly" />
		</div>
	</div>
	<div class = "row pd_top_xs pd_bottom_xs border">
		<div class = "col-xs-12 col-md-3">Amount</div>
		<div class = "col-xs-12 col-md-3">
			<input type = "text" name = "amount" id = "amount" value = "<?php echo set_value('amount'); ?>" />
		</div>
		<div class = "col-xs-12 col-md-3">Payment Received</div>
		<div class = "col-xs-12 col-md-3">
			<input type = "text" name = "payment_received_date" id = "payment_received_date" class = "date_picker" value = "<?php echo set_value('payment_received_date'); ?>" readonly = "readonly" />
		</div>
	</div>
	<div id = "pf_holder" class = "row pd_top_xs pd_bottom_xs">
		<input type = "hidden" name = "file_counter" id = "file_counter" value = "1" />
		<div class = "col-xs-12">
			<div class = "col-xs-6">
				<input type = "file" name = "payment_file[]" />
			</div>
			<div class = "col-xs-6">
				<input type = "button" name = "add_file_btn" id = "add_file_btn" value = "+" onclick = "return addMoreFile();" title = "Add More File" />
			</div>	
		</div>
	</div>	
	<div class = "row pd_top_xs pd_bottom_xs">	
		<div class = "col-xs-12 col-md-6">
			<input type = "submit" name = "submit_add_payment" id = "submit_add_payment" value = "Add New Payment" class = "btn" />
		</div>	
	</div>
</form>	

	
