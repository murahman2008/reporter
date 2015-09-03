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

function checkDateWithFormat($inputDate, $format)
{
	$d = DateTime::createFromFormat($format, $inputDate);
	if(!$d || $d->format($format) !== $inputDate)
		return false;

	return true;
}


function convertDate($inputDate, $inputFormat = 'd/m/Y', $outpuFormat = 'Y-m-d')
{
	$d = DateTime::createFromFormat($inputFormat, $inputDate);
	if(!$d || $d->format($inputFormat) != $inputDate)
		throw new Exception($inputDate.' is not a valid date');
	else
		$output = $d->format($outpuFormat);
	
	return $output;
}