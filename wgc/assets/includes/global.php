<?php
	// Import our devkey file
	require_once '~devkey.inc';
	
	// Configure global error handling
	if(DG_DEBUG_MODE)
	{
		error_reporting(-1);
		ini_set('display_errors', 'on');
	}
	
	else
	{
		error_reporting(0);
		ini_set('display_errors', 'off');
	}
	
	// Import our classes
	require_once 'bin/DeveloperErrorHandler.php'; // Should always be imported first
	require_once 'bin/CookieInterface.php';
	require_once 'bin/GeoIP.php';
	require_once 'bin/Browser.php';
	require_once 'bin/SQLFactory.php';
	require_once 'bin/STR.php';
	require_once 'bin/Time.php';
	require_once 'bin/Controller.php';
	
	// Set default developer directive values if not already defined
	if(!defined('DG_DEBUG_MODE')) define('DG_DEBUG_MODE', FALSE); // Should ALWAYS be set to 'FALSE' on live servers
	if(!defined('DG_REAL_HOST')) die('DG_REAL_HOST does not exist!');
	if(!defined('DG_REAL_HOST_NAME')) die('DG_REAL_HOST_NAME does not exist!');
	if(!defined('DG_REAL_SCRIPT_NAME')) define('DG_REAL_SCRIPT_NAME', substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], "/") + 1, -4));
	
	// Set Referer
	if(!defined('DG_REAL_HOST'))
	{
		if(isset($_SERVER['HTTP_REFERER'])) define('DG_REFERRER', current(explode('?', urldecode($_SERVER['HTTP_REFERER']))));
		else define('DG_REFERRER', DG_REAL_HOST);
	}
	
	// Set the appropriate content-type
	$data = Browser::detect();
	if(($data->browser != BROWSER_IE && $data->browser != BROWSER_UNKNOWN_BOT) || ($data->browser == BROWSER_IE && $data->version >= 9))
		header('Content-Type: application/xhtml+xml');
	
	// Protect page from direct access
	if(count(get_included_files()) <= 1)
	{
		header('Location: '.DG_REAL_HOST);
		exit;
	}
	
	// Begin a session on each page.
	session_start();
?>