<?php
namespace Lib;

/**
 * @desc: Helper Classes
 */

// custom Exception class
class Helper extends Config
{
	
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Handle Exception object
	 * @param  [obj] $e Exception object
	 * @return [type]   [description]
	 */
	
	// try {
	//     throw new Exception('Error message');
	// } catch (Exception $e) {
	//     helper::handle_error($e);
	// }
	public static function handle_error($e)
	{
		echo "<pre>";
		echo "message: " . $e->getMessage() . "<br />";
		echo "file: " . $e->getFile() . "<br />"; 
		echo "line: " . $e->getLine() . "<br />"; 
		echo "</pre>";
	}

	public static function debug($data, $mode="p")
	{
		echo "<pre>";
		if ($mode == "v")
			var_dump($data);
		else if ($mode == "p")
			print_r($data);
		else echo $data;
		echo "</pre>";
		echo "<br />---------------------------------------<br />";
	}

	/**
	 * log events
	 * @param: $message
	 * @return: none
	 */
	function log_message($log_file, $message)
	{
		$log  = file_get_contents($log_file);
		$log .= $message . "\n";
		if (file_put_contents($log_file, $log) === FALSE)
			echo "cant log error";
	}

	/**
	 * return current date and time
	 * @param: none
	 * @return: str
	 */
	public static function datetime_now()
	{
		return date("M-d-y h:i:s");
	}

	/**
	 * remove non alpha-numeric char
	 */
	public static function clean_str($str)
	{
		return preg_replace("/[^A-Za-z0-9]/", '', $str);
	}


}














?>