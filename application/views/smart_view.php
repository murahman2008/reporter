<html>
	<head>
		<title><?php echo ((isset($pageTitle) && trim($pageTitle) !== '') ? $pageTitle : 'Reporter'); ?></title>
		
		<!-- ALL Javascripts -->
		<script type = "text/javascript" language = "javascript" src = "<?php echo base_url('javascript/jquery/jquery.js'); ?>"></script>
		<script type = "text/javascript" language = "javascript" src = "<?php echo base_url('javascript/fancybox/jquery.fancybox.js')?>"></script>
		<script type = "text/javascript" language = "javascript" src = "<?php echo base_url('javascript/bootstrap/bootstrap.min.js')?>"></script>
		<script type = "text/javascript" language = "javascript" src = "<?php echo base_url('javascript/main.js')?>"></script>
		
		<!-- ALL Css -->
		<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/fancybox/jquery.fancybox.css'); ?>">
		<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/bootstrap/bootstrap.css'); ?>">
		<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/bootstrap/bootstrap-theme.min.css'); ?>">
		<link rel = "stylesheet" type = "text/css" href = "<?php echo base_url('css/main.css'); ?>">
	</head>
	<body>
		<?php if(isset($source) && ($source = trim($source)) !== '') : ?>
		
			<?php if(isset($header) && $header === true ) : ?>
				Here comes the header
			<?php endif; ?>
			
			<div class = "container">
				<?php 
					$this->load->view($source, (isset($data) && is_array($data) ? $data : array()));
				?>
			</div>	
			
			<?php if(isset($footer) && $footer === true ) : ?>
				Here comes the Footer
			<?php endif; ?>
			
		<?php else : ?>
			<h4>Sorry... The page you are looking for has not been specified</h4>
		<?php endif; ?>
		
	</body>
</html>


