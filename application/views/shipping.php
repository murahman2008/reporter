<script type = "text/javascript" src = "<?php echo base_url('javascript/jquery/jquery.js'); ?>"></script>
<script type = "text/javascript" src = "<?php echo base_url('javascript/jquery/jquery-ui.js'); ?>"></script>

<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/jquery-ui.css'); ?>">
<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/jquery-ui.structure.css'); ?>">
<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/jquery-ui.theme.css'); ?>">

<input type = "text" name = "postcode" id = "postcode" placeholder = "Postcode" />
<input type = "hidden" name = "hdn_shipping_code" id = "hdn_shipping_code" value = "lite" />
<div id = "shipping_cost_result"></div>

<script type = "text/javascript" language = "javascript">
	$(function() {
		$('#postcode').autocomplete({
			source: 'http://localhost/reporter/ajax/shipping',
			minLength: 4,
			select: function(event, ui) {
				var postcodeId = ui.item.id;

				if($.trim(postcodeId) === '0')
				{	
					$('#shipping_cost_result').html('Postcode ' + $('#postcode').val() + ' Not Found in the system');
					$('#postcode').val('');
				}	
				else
				{	
					$('#postcode').val(ui.item.value);
					$.ajax({
						url: 'http://localhost/reporter/ajax/calculate_shipping',
						dataType: 'json',
						method: 'post',
						data: {
							'postcode_id': postcodeId,
							'shipping_code': $.trim($('#hdn_shipping_code').val())
						},
						success: function(data) {
							if(!data.status)
								alert(data.message);
							else
								$('#shipping_cost_result').html(data.data);
	
							return false;
						}
					});
				}	

				return false;		
			}
		}).keypress(function (e) {
			if(e.which == 13) 
				return false;
			else
				$('#shipping_cost_result').html('');
		});
	});	
</script>
