<?php if(isset($reportTypeArray) && is_array($reportTypeArray) && count($reportTypeArray) > 0) : ?>
	<?php foreach($reportTypeArray as $key => $value) : ?>
		
		<div class = "row report_main_row border round_border pd_xs" style = "margin-top:10px;" report_type_id = "<?php echo $value->id; ?>" 
			user_id = "<?php echo $user['id']; ?>" working_date = "<?php echo $workingDate; ?>">
			
			<div class = "col-xs-2"><?php echo $value->name; ?></div>
			<div class = "col-xs-2">
				<input type = "text" name = "<?php echo 'current_counter_'.$value->id; ?>" id = "<?php echo 'current_counter_'.$value->id; ?>" 
					value = "<?php echo (isset($rcData[$user['id']]['report_counters'][$workingDate][$value->id]) ? $rcData[$user['id']]['report_counters'][$workingDate][$value->id]['rc_counter'] : 0); ?>" readonly = "readonly" />
			</div>
			<div class = "col-xs-2">
				<div class = "col-xs-4">
					<input type = "button" class = "btn btn-danger bold action_button" operator = "-" name = "<?php echo 'minus_btn_'.$value->id; ?>" id = "<?php echo 'minus_btn_'.$value->id; ?>" value = "-" />
				</div>	
				<div class = "col-xs-4">
					<input type = "text" class = "counter_box" name = "<?php echo 'counter_'.$value->id; ?>" id = "" value = "1" style = "width:50px;" />
				</div>
				<div class = "col-xs-4">
					<input type = "button" class = "btn btn-success bold action_button" operator = "+" name = "<?php echo 'plus_btn_'.$value->id; ?>" id = "<?php echo 'plus_btn_'.$value->id; ?>" value = "+" />
				</div>	
			</div>
		</div>
		
	<?php endforeach;?>
<?php else : ?>	
	<h3>Cannot find any report types to display..</h3>
<?php endif; ?>

