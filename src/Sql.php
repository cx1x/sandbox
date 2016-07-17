<?php
namespace Lib;

use Mysqli;
use Exception;

/**
* SQL Base class
* Author: Christian R. <webdev@megasportsworld.com>
* Version: 1.0
* Copyright: 2016
**/


class Sql extends Helper {


private static $instance;
private $handler;
private $dbhost;
private $dbuser;
private $dbpassword;
private $dbname;

	
	/**
	* Get an instance of the Database
	* @return obj instance
	*/

	public static function get_DB_instance()
	{
		if (!self::$instance)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}



	function __construct()
	{
		
	}


	// magic method clone is empty to prevent duplication of connection
	private function __clone() { }


	/**
	 * DB connection
	 * @param  [arr] $ini config (host, username, password, dbname)
	 * @param  [str] $dbname (database name)
	 * @return [bool] [true: false]
	 */
	public function connect_db($ini, $dbname='')
	{
	
		try {

			$config = $ini->get_config();
			if (isset($config['dbname'])) 	$this->dbname = $config['dbname'];
			if (!empty($dbname)) 			$this->dbname = $dbname;
			if (empty($this->dbname)) {
				throw new Exception('No database selected.');
				return false;
				}

			switch ($ini->get_env()) {
				case 'local':
					$this->dbhost 		= $config['local_host'];
					$this->dbuser	 	= $config['local_username'];
					$this->dbpassword 	= $config['local_password'];
					break;
				
				case 'production':
					$this->dbhost 		= $config['server_host'];
					$this->dbuser	 	= $config['server_username'];
					$this->dbpassword 	= $config['server_password'];
					break;

				default:
					# code...
					break;
				}

				$this->handler[$this->dbname] = new mysqli(
														$this->dbhost, 
														$this->dbuser, 
														$this->dbpassword, 
														'wls2_'.$this->dbname
														);	
			
				if (mysqli_connect_errno())
		        {
		        	throw new Exception('SQL ERROR: Can\'t connect to MySql');
					return false;
		        }

		        return $this->handler[$this->dbname];
	      
		} 
		catch (Exception $e) {
			helper::handle_error($e);
		}
		
	}


	function get_live_score($handler, $sport='')
	{

	}



	/**
	* Insert record
	* @param obj 	$handler DB connection
	* @param str 	$table Table name
	* @param array  $data Field on `key`; data on `data`
	* @param array  $primary_key key to check for duplicate, value for update
	* @return bool TRUE; FALSE if query failed
	*/
	function insert_record($handler, $table, $data, $primary_key=array())
	{
	
		$_infoField = $_infoData = array();

		$i = 0;
		
		$len = count($data);

		$_string = '';
		
		foreach($data as $key => $val){
		
			$_infoData[$key] = "'{$val}'";
			
			if(is_array($val)){
			
				$_infoData[$key] = "'".json_encode($val)."'";
			
				if($i == $len - 1)
					$_string .= $key . '=\'' . json_encode($val) .'\'';
				else
					$_string .= $key . '=\'' . json_encode($val) .'\',';
			
			}
			
			else{
			
				$_infoData[$key] = "'{$val}'";
			
				if($i == $len - 1)
					$_string .= $key . '=\'' . $val .'\'';
				else
					$_string .= $key . '=\'' . $val .'\',';
			
			}
			
			$_infoField[$key] = $key;

			$i++;
		
		}
	
		$_fields = implode( ', ', $_infoField);
		
		$_fieldData = implode( ', ', $_infoData);

		$sql = "INSERT INTO `{$table}` ({$_fields}) VALUES ({$_fieldData})  on DUPLICATE KEY UPDATE " . $_string;
		
		// echo $sql
		
		// echo $sql . '<br/>';
		
		// exit;

		try {
				if (!is_object($handler))
				{
					throw $err = new Exception("ERROR: Not a valid MySQL connection <br/> ");
					return FALSE;	
				}

				if ($handler->query($sql) === FALSE)
				{
					throw $err = new Exception("SQL ERROR: " .$handler->error . " <br/> " . $sql);
					return FALSE;
				}	
			}
			catch (Exception $e)
			{
				helper::handle_error($e);
			}
	}


	/**
	* Insert record
	* @param obj 	$handler DB connection
	* @param str 	$table Table name
	* @param array  $data Field on `key`; data on `data`
	* @param array  $primary_key key to check for duplicate, value for update
	* @return bool TRUE; FALSE if query failed
	*/
	function b_insert_record($handler, $table, $data, $primary_key=array(), $score='', $type='')
	{
		
		$keys = implode(', ', array_keys($data));
		$values = "'" .  implode("','", array_values($data)) . "'";
		$sql = "INSERT INTO `{$table}` ({$keys}) VALUES ({$values})";

		if (!empty($primary_key))
		{
			//$key = array_key();
			//print_r($primary_key);
			//exit;

			$sql .= " ON DUPLICATE KEY UPDATE `". key($primary_key) ."` = '". $primary_key[key($primary_key)] ."', ";
			unset($data[key($primary_key)]);

			$exclude = array('score', 'score_final');

			foreach ($data as $key => $val) {

				// exclude score if blank on update if match already in database
				if (empty($score) && $type == "match") {

					if ($key != "score" && $key != "score_final" && $key != "result") {
						$updates[] = "{$key} = '{$val}'";
					}

				}
				else 
				{
					$updates[] = "{$key} = '{$val}'";

					if ($type == "match" && $key == "score") echo "SCORE DATA [ MATCH ID ] " . $primary_key[key($primary_key)] . " --> score: " . $score . "<br /><br />";
				}

			}

			

			//if ($primary_key[key($primary_key)] == "9612519") {
			//	echo "<pre>";
			//	print_r($updates);
			//	echo "<pre/>";
			//}

			$implode_array = implode(', ',$updates);
			$sql .= " {$implode_array}";

			if (empty($score)  && $type == "match") {
				echo "NOT OVER-WRITING " . $primary_key[key($primary_key)] . " --> SQL " . $sql . "<br /><br />";
			}
		
			//echo '<pre>';
			//echo $sql;
			//echo '</pre>';
		}
		//exit;

		try {
				if (!is_object($handler))
				{
					throw $err = new Exception("ERROR: Not a valid MySQL connection <br/> ");
					return FALSE;	
				}

				if ($handler->query($sql) === FALSE)
				{
					throw $err = new Exception("SQL ERROR: " .$handler->error . " <br/> " . $sql);
					return FALSE;
				}	
			}
			catch (Exception $e)
			{
				helper::handle_error($e);
			}
	}



	/**
	* Get record
	* @param obj   $handler DB connection handler 
	* @param str   $table Table name
	* @param arr   $cols Column names to select
	* @param str   $where String to concatenate as `where`
	* @param int   $limit Limit of returned rows
	* @return arr  records; FALSE if failed
	*/	
	function get_record($handler, $table, $cols, $where='', $limit=100)
	{
		
		$columns = "" .  implode(", ", array_values($cols)) . "";
		$sql = "SELECT {$columns} FROM `{$table}` ";
		if (!empty($where)) $sql .= " WHERE ".$where. " LIMIT ".$limit; 
		$row = array();

		try {
				if (!is_object($handler))
				{
					throw $err = new Exception("ERROR: Not a valid MySQL connection <br/> ");
					return FALSE;	
				}
				
				if (!$result = $handler->query($sql))
				{
					throw $err = new Exception("SQL ERROR: " .$handler->error . " <br/> " . $sql);
					return FALSE;
				}
				else 
				{
					while ($obj = $result->fetch_object()) 
				    {
				        $row[] = (array)$obj;
				    }

				    return $row;
				}	
			
			} 
			catch (Exception $e) 
			{
				helper::handle_error($e);
			}

	}



	function exec_query($handler, $sql, $designated_key='')
	{
		
		//echo "im exec query";
		//echo "<pre>";
		//print_r($sql);
		//echo "</pre>";
		$row = array();

		try {
				if (!is_object($handler))
				{
					//echo "not object";
					throw $err = new Exception("ERROR: Not a valid MySQL connection <br/> ");
					return FALSE;	
				}
				
				if (!$result = $handler->query($sql))
				{
					//echo "result error";
					//echo "<br />";
					//echo "sql: ".$sql;


					throw $err = new Exception("SQL ERROR: " .$handler->error . " <br/> " . $sql);
					return FALSE;
				}
				else 
				{
					while ($obj = $result->fetch_object()) 
				    {
				        if (!empty($designated_key))
				        	$row[$obj->$designated_key][] = (array)$obj;
				        else
				        	$row[] = (array)$obj; 
				    }

				    return $row;
				}	
				
			} 
			catch (Exception $e) 
			{
				helper::handle_error($e);
			}

	}


	function sanitize_shit($handler, $str)
	{
		//echo "sweet hello! ".$str;	
		//return $handler->real_escape_string($str);
		try {
				if (!is_object($handler))
				{
					throw $err = new Exception("ERROR: Not a valid MySQL connection <br/> ");
					return FALSE;	
				}

				if ($handler->real_escape_string($str) === FALSE)
				{
					throw $err = new Exception("SQL ERROR: " .$handler->error . " <br/> " . $sql);
					return FALSE;
				}	

				return $handler->real_escape_string($str);
			}
			catch (Exception $e)
			{
				helper::handle_error($e);
			}
	}



	/**
	* @param: none
	* @return: none
	*/
	function close_db()
	{
		//unset $this->sql_handler
	}
	
}
