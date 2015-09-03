<?php if(isset($payment) && is_array($payment)) : ?>
	<?php if(count($payment) > 0 ) : ?>
		<div class = "row">
			<div class = "col-xs-12 page_title">View Payment</div>
		</div>
		<?php foreach($payment as $p) : ?>
			<div class = "row border" style = "font-size:10pt;">
				<div class = "col-xs-12 col-md-1 pd_left_xs bold">Start Date:</div>
				<div class = "col-xs-12 col-md-2"><?php echo $p['work_start_date']; ?></div>
				<div class = "col-xs-12 col-md-1 bold">End Date:</div>
				<div class = "col-xs-12 col-md-2"><?php echo $p['work_end_date']; ?></div>
				<div class = "col-xs-12 col-md-1 bold">Amount:</div>
				<div class = "col-xs-2 col-md-2"><?php echo '$ '.$p['amount']; ?></div>
				<div class = "col-xs-12 col-md-1 bold">Received:</div>
				<div class = "col-xs-2 col-md-2"><?php echo $p['payment_received_date']; ?></div>
			</div>		
				
			<?php if(isset($p['payment_files']) && count($p['payment_files']) > 0 ) : ?>
				<?php foreach($p['payment_files'] as $pf) : ?>
					<div class = "row border bold" style = "font-size:10pt;">
						<div class = "col-xs-12">
							<a href = "<?php echo base_url($pf['full_path']); ?>" target = "_blank">
								<span class="glyphicon glyphicon-file" aria-hidden="true"></span>
								<?php echo $pf['file_name']; ?>
							</a>	
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
							
		<?php endforeach; ?>				
	<?php else : ?>
		<h3>Invalid Payment information provided.</h3>
	<?php endif; ?>
<?php else : ?>
	<h3>Internal Error!!! Failed to display Payment Info.</h3>
<?php endif; ?>

