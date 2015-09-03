<html>
	<head>
		<title><?php echo ((isset($pageTitle) && trim($pageTitle) !== '') ? $pageTitle : 'Reporter'); ?></title>
		
		<link rel = "icon" type = "image/png" href = "<?php echo base_url('images/grid_favicon.png'); ?>" />
		
		<!-- ALL Javascripts -->
		<script type = "text/javascript" src = "<?php echo base_url('javascript/jquery/jquery.js'); ?>"></script>
		<script type = "text/javascript" src = "<?php echo base_url('javascript/fancybox/jquery.fancybox.js')?>"></script>
		<script type = "text/javascript" src = "<?php echo base_url('javascript/bootstrap/bootstrap.min.js')?>"></script>
		<script type = "text/javascript" src = "<?php echo base_url('javascript/jquery/jquery-ui.js')?>"></script>
		<script type = "text/javascript" src = "<?php echo base_url('javascript/main.js')?>"></script>
		
		<!-- ALL Css -->
		<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/fancybox/jquery.fancybox.css'); ?>">
		<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/bootstrap/bootstrap.css'); ?>">
		<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/bootstrap/bootstrap-theme.min.css'); ?>">
		<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/jquery-ui.css'); ?>">
		<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/jquery-ui.structure.css'); ?>">
		<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/jquery-ui.theme.css'); ?>">
		<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/main.css'); ?>">
	</head>
	<body>
		<?php if(isset($source) && ($source = trim($source)) !== '') : ?>
		
			<?php if(isset($header) && $header === true ) : ?>
				<?php 
					$this->load->view('header');
				?>
			<?php endif; ?>
			
			<div class = "container">
				<?php 
					$errorMsg = getSessionData('view_error_msg');
					
					if($errorMsg !== false)
					{
						if(is_array($errorMsg))
						{
							if(count($errorMsg) > 0)
								echo '<div class = "row error_msg_div mg_botton_xs">'.implode("<br/>", $errorMsg).'</div>';
						}
						else if(trim($errorMsg) !== '')
							echo '<div class = "row error_msg_div mg_botton_xs">'.trim($errorMsg).'</div>';
	
						clearSessionData('view_error_msg');
					}
						
					$successMsg = getSessionData('view_success_msg');
					
					if($successMsg !== false)
					{
						if(is_array($successMsg))
						{
							if(count($successMsg) > 0)
								echo '<div class = "row success_msg_div mg_botton_xs">'.implode("<br/>", $successMsg).'</div>';
						}
						else if(trim($successMsg) !== '')
							echo '<div class = "row success_msg_div mg_botton_xs">'.trim($successMsg).'</div>';
	
						clearSessionData('view_success_msg');
					}	
				?>
			
				<?php 
					$this->load->view($source, (isset($data) && is_array($data) ? $data : array()));
				?>
			</div>	
			
			<?php if(isset($footer) && $footer === true ) : ?>
				<?php $this->load->view('footer'); ?>
			<?php endif; ?>
			
		<?php else : ?>
			<h4>Sorry... The page you are looking for has not been specified</h4>
		<?php endif; ?>
		
	</body>
</html>


