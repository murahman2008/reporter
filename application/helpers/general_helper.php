<?php

function inspect($data, $raw = false)
{
	if(!$raw)
	{	
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
	else
		var_dump($data);	
}