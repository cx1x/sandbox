<?php

namespace Lib;

/**
 * loads configuration settings
 *
 * @package default
 * @author 
 **/
class Config 
{

	private $config;
	private $env;


	/**
	 * loads configuration variables
	 * @param [str] $path [path/to/config.ini]
	 */
	function __construct($path)
	{
		$this->config = parse_ini_file($path);
	}


	protected function get_config()
	{
		return $this->config;
	} 


	function get_env()
	{
		if (isset($_SERVER)) {
			$local_env = explode(",",$this->config['local_env']);
			
			//print_r($this->config['local_env']);
			//print_r($this->config);
			//echo $_SERVER['SERVER_NAME'];
			//print_r($local_env);
			$this->env = (in_array($_SERVER['SERVER_NAME'], $local_env)) ? 'local' : 'production'; 
		}
		else {
			$this->env = "cron";
		}
		return $this->env;
	}





} // END class Config 


