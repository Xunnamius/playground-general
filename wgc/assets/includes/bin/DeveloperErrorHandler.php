<?php
	/*------------------------- START COMMENT BLOCK -------------------------

	DeveloperErrorHandler, by Xunamius of Dark Gray (2010).
	http://darkgray.org/devs/Xunnamius

	You are free to use this code for personal/business use as
	long as this header (this whole comment block) remains
	intact and is presented as is with no modifications.

	--------------------------- END COMMENT BLOCK ---------------------------*/
	
	/*
	 * DeveloperErrorHandler Class
	 * 
	 * This class acts as a common interface for PHP developers
	   who need a little custom error handling without hijacking
	   PHP's whole error/warning handling system.
	 *
	 * This class provides functionality to quickly and easily
	   throw custom errors, fatal errors, warnings, notices, and
	   debug messages (at the behest of the DG_DEBUG_MODE directive).
	 *
	 * Aliases:
	 *		- DEH
	 *
	 * Properties:
	 *		- Singleton
	 *		- Independent Interface
	 * 		- Extensible
	 *
	 * Pertinent Directives:
	 * 		- DG_DEBUG_MODE		(BOOL)	toggles debug mode on (TRUE) or off (FALSE)
	 * 
	 * Class-specific Constants:
	 *		- E_USER_DEBUG
	 *
	 * Dependencies:
	 * 		(none)
	 *
	 * Plugins:
	 * 		(none)
	 *
	 * Audience: PHP 5.3.3
	 *
	 * Version: 1.25
	 */
	class DeveloperErrorHandler
	{	
		/* PHP Magic Method __construct() in Singleton mode */
		private function __construct(){}
		
		/* Used to manage exceptions within (and without) the DeveloperErrorHandler class. */
		protected static function handler($errlvl, $errmsg, $errfile, $errline, $errcontext)
		{
			switch($errlvl)
			{
				case E_USER_ERROR:
					echo '<br /><strong>Developer Error:</strong> ', $errmsg, '<br />';
					if(DG_DEBUG_MODE) echo 'PHP ', PHP_VERSION, ' (', (PHP_OS == 'WINNT' ? 'Windows' : PHP_OS), ')<br />';
					exit('<span style="color: red;">Execution Aborted.</span>');
					break;
			
				case E_USER_WARNING:
					echo '<br /><strong>Developer Warning:</strong> ', $errmsg, '<br />';
					break;
			
				case E_USER_NOTICE:
					if(substr($errmsg, 0, 7) == 'debug: ')
					{ if(DG_DEBUG_MODE) echo '<br /><strong>Developer Debug:</strong> ', substr($errmsg, 7), '<br />'; }
					else echo '<br /><strong>Developer Notice:</strong> ', $errmsg, '<br />';
					break;
				
				// Unknown error level?!
				default:
					echo '<br />Unknown developer error type <strong>', (!isset($errlvl) ? $errlvl : '(empty)'), '</strong><br />';
					echo 'Additionally: ';
					return false; // Execute PHP internal error handler instead
					break;
			}
		
			// Don't execute PHP internal error handler
			return true;
		}
		
		/*
		 * public static void except ( string $errmsg [, integer $errlvl = E_USER_WARNING [, integer $recursionlvl = 0]] )
		 *
		 * Throws a customized developer error.
		 * Check http://php.net/manual/en/function.trigger-error.php for acceptable $errlvl arguments
		   (note that there is an extra argument: E_USER_DEBUG -- for debug messages).
		 *
		 * Do note that recursionlvl should be set to however deep the call comes from. If it comes
		   from within, say, a class call for instance. The recursion level should be 2. If it comes
		   from within a class call that is within another class call, it should be 3. If it comes
		   from within a class call that is within a class call that is within a class call, the
		   recursion level should be 4. Et Cetera.
		 *
		 * @param string errormsg the message to forward to the browser
		 * @param Integer[optional] errlvl error level
		 * @param Integer[optional] recursionlvl msg recursion level
		 *
		 * @return nothing
		 */
		public static function except($errmsg, $errlvl=E_USER_WARNING, $recursionlvl=0)
		{
			// Get the calling function and details about it
			$callee = debug_backtrace();
			$callee = $callee[$recursionlvl];
			
			// Ensure the use of our custom handler
			set_error_handler('DeveloperErrorHandler::handler');
			
			if($errlvl == E_USER_DEBUG) trigger_error('debug: <em>'.$errmsg.'</em> in <strong>'.$callee['file'].'</strong> on line <strong>'.$callee['line'].'</strong>.', E_USER_NOTICE);
			else trigger_error('<em>'.$errmsg.'</em> in <strong>'.$callee['file'].'</strong> on line <strong>'.$callee['line'].'</strong>.', $errlvl);
			
			// Return the error handler to its proper state (we don't want to hijack PHP's EH!)
			restore_error_handler();
		}
	}
	
	/* Alias of DeveloperErrorHandler (snytactic sugar) */
	final class DEH extends DeveloperErrorHandler
	{
		private function __construct(){}
		public static function except($errmsg, $errlvl=E_USER_WARNING, $recursionlvl=1)
		{ parent::except($errmsg, $errlvl, $recursionlvl); }
	}
	
	/* Class-specific Constants */
	define('E_USER_DEBUG', -1); // Make sure E_USER_DEBUG is a real thing.
	
	// Protect page from direct access
	if(count(get_included_files()) <= 1) die('<h1 style="color: red; font-weight: bold;">No.</h1>');
?>