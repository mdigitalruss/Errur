<?php
/*
 * Errur
 * Simple class for integrating Airbrake and displaying an error screen too
 *
 * Errur class Copyright (c) 2013 Razor Studios
 * Airbrake classes Copyright 	(c) 2011 Drew Butler <drew@abstracting.me>
 * (http://www.opensource.org/licenses/mit-license.php)
 */

require_once 'Airbrake/Client.php';
require_once 'Airbrake/Configuration.php';

class Errur
{
	protected static $instance = null;
	protected $template = null;
	protected $AirbrakeClient = null;

	public function __construct($template, $AirbrakeClient)
	{
		// Use our own fancy error handler
		// Do not call directly, use errur::init($template_markup)
		$this->template = $template;
		$this->AirbrakeClient = $AirbrakeClient;
	}

	public static function init($template, $AirbrakeAPIKey, $is_dev)
	{
		if (!isset(self::$instance)) {
                
			// Dev or Live?
			$deployment = ($is_dev)? "Development" : "Production";

			// Init Airbrake, yay
			$AirbrakeConfig = new Airbrake\Configuration($AirbrakeAPIKey, array('environmentName' => $deployment));
			$AirbrakeClient = new Airbrake\Client($AirbrakeConfig);

			self::$instance = new self($template, $AirbrakeClient);

			set_error_handler(array(self::$instance, 'onError'));
            set_exception_handler(array(self::$instance, 'onException'));
		}

		return self::$instance;
	}

	public function onError($errorNumber, $errorString, $errorFile, $errorLine)
	{
		// Get stack trace
		$backtrace = array();
		foreach (debug_backtrace() as $p => $t) {
            $backtrace[] = (isset($t['class'])? $t['class'] : '' ) . (isset($t['type']) ? $t['type'] : '') . (isset($t['function']) ? $t['function'] : '{main}') . '():' . (isset($t['line']) ? $t['line'] : 'unknown');
		}
		$backtrace = implode('<br/>', array_reverse($backtrace));

		// Build keywords
		$keywords = array('[ERROR_NUMB]' => $errorNumber, '[ERROR_MSG]' => $errorString, '[ERROR_FILE]' => $errorFile, '[ERROR_LINE]' => $errorLine, '[ERROR_TRACE]' => $backtrace);

		// Replace keywords
		foreach ($keywords as $keyword => $content) {
			$this->template = str_replace($keyword, $content, $this->template);
		}

		// Clear screen
		ob_end_clean();

		// Print out template
		echo $this->template;

		// Notify Airbrake
		$this->AirbrakeClient->notifyOnError($errorString);

		// Kill output
		die();
	}

	public function onException($exception)
	{
		// Get stack trace
		$backtrace = $exception->getTrace(); 
        $backtrace_str = $exception->getTraceAsString();

		// Build keywords
		$keywords = array('[ERROR_NUMB]' => $exception->getCode(), '[ERROR_MSG]' => $exception->getMessage(), '[ERROR_FILE]' => $exception->getFile(), '[ERROR_LINE]' => $exception->getLine(), '[ERROR_TRACE]' => $backtrace_str);

		// Replace keywords
		foreach ($keywords as $keyword => $content) {
			$this->template = str_replace($keyword, $content, $this->template);
		}

		// Clear screen
		ob_end_clean();

		// Print out template
		echo $this->template;

		// Notify Airbrake
		$this->AirbrakeClient->notifyOnError($exception->getMessage(), $backtrace);

		// Kill output
		die();
	}
}
?>
