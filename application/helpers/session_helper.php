<?php

function startSession()
{
	session_start();	
}

function destroySession()
{
	session_destroy();
}

function checkLoggedUser()
{
	return ((isset($_SESSION['user']) && (is_array($_SESSION['user']) || is_object($_SESSION['user']))) ? $_SESSION['user'] : false);
}

function checkAdminRole(Array $role, $strictAdmin = false)
{
	if($strictAdmin)
		return ((isset($role['id']) && trim($role['id']) === trim(ROLE_ID_ADMIN)) ? true : false); 
	else
		return ((isset($role['priority']) && $role['priority'] >= ROLE_PRIORITY_MANAGER) ? true : false); 
}

function checkContractorRole(Array $role)
{
	return ((isset($role['id']) && trim($role['id']) === trim(ROLE_ID_MEDICAL_CONTRACTOR)) ? true : false);
}

function getSessionData($key = '')
{
	if(($key = trim($key)) === '')
		return false;
	return (isset($_SESSION[$key]) ? $_SESSION[$key] : false);
}

function setSessionData($key, $data)
{
	if(($key = trim($key)) === '')
		return false;
	
	clearSessionData($key);
	$_SESSION[$key] = $data;
	
	return true;
}

function clearSessionData($key)
{
	if(isset($_SESSION[$key]))
		unset($_SESSION[$key]);
	
	return true;
}

startSession();
