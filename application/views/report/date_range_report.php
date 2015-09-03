<div class = "row mg_bottom_xs">
	<div class = "col-xs-12 page_title">Date Range Report</div>
</div>

<form name = "date_range_report_form" id = "date_range_report_form" method = "post" action = "<?php echo base_url('report/date_range_report'); ?>" style = "font-size:10pt;">
	<div class = "row">
		<div class = "col-xs-1 bold zero_padding cetner">Start Date</div>
		<div class = "col-xs-3">
			<input type = "text" class = "date_picker" name = "report_start_date" id = "report_start_date" value = "<?php echo set_value('report_start_date', ''); ?>" readonly = "readonly" placeholder = "Start Date..." />
		</div>
		<div class = "col-xs-1 bold">End Date</div>
		<div class = "col-xs-3">
			<input type = "text" class = "date_picker" name = "report_end_date" id = "report_end_date" value = "<?php echo set_value('report_end_date', ''); ?>" readonly = "readonly" placeholder = "End Date..." />
		</div>
		<div class = "col-xs-2 bold">Report Type</div>
		<div class = "col-xs-2">
			<select name = "report_type" id = "report_type">
				<option value = "0" <?php echo set_select('report_type', '0', true); ?> >All Report Types</option>
				
				<?php foreach($reportTypeArray as $reportType) : ?>
					<option value = "<?php echo $reportType->id; ?>" <?php echo set_select('report_type', $reportType->id); ?> ><?php echo $reportType->name; ?></option>		
				<?php endforeach; ?>
			</select>
		</div>		
		<input type = "hidden" name = "selected_user" id = "selected_user" value = "<?php echo ($selectedUser !== false ? $selectedUser['id'] : 0); ?>" />
	</div>
	<div class = "row mg_top_xs">
		<div class = "col-xs-12 col-md-2 zero_padding">
			<input type = "submit" class = "btn btn-success" name = "date_range_report_submit" id = "date_range_report_submit" value = "Generate Report" />
		</div>
	</div>
</form>

<?php if(isset($reportResult)) : ?>
	<?php 
		foreach($reportResult as $key => $value) 
		{
			echo '<div class = "row pd_bottom_xs pd_top_xs mg_top_md border" style = "font-size:10pt; color:#336699;">
					<div class = "col-xs-12 col-md-3 bold">Name: '.$value['first_name'].' '.$value['last_name'].'</div>
					<div class = "col-xs-12 col-md-3 bold">Email: '.$value['email'].'</div>
					<div class = "col-xs-12 col-md-3 bold">Phone: '.$value['phone'].'</div>
					<div class = "col-xs-12 col-md-3 bold">Username: '.$value['username'].'</div>';

			if(count($value['report_counters']) > 0)
			{
				foreach($value['report_counters'] as $k => $v)
				{
					echo '<div class = "col-xs-12 mg_top_xs bold">Working Date : '.convertDate($k, 'Y-m-d', 'd/m/Y').'</div>';

					echo '<div class = "col-xs-12">
							<table width = "100%" border = "1" style = "font-size:10pt;">
								<tr style = "background-color:#336699; color:white;">';
					
					foreach($reportTypeArray as $rt)
						echo '<td class = "center bold">'.$rt->name.'</td>';
					
					echo '</tr>';
					echo '<tr>';
					
					foreach($reportTypeArray as $rt)
					{
						echo '<td class = "center bold">';
						
						if(isset($v[$rt->id]))
							echo $v[$rt->id]['counter'];
						else
							echo '<span style = "color:red;">0</span>';
						
						echo '</td>';
					}
					
					echo '</tr>
						</table>
					</div>';
					
					echo '<hr/>';
				}
			}

			echo '</div>';
			//echo '<div class = "row"><div class = "col-xs-12 center">***********************************</div></div>';
		}
	?>
<?php endif; ?>