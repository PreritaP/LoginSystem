<?php

/**
 * Datebase: Class for handling database related requests
 */
class Database 
{
	
	private $host 		= DB_HOST;
	private $database 	= DB_DATABASE;
	private $username 	= DB_USERNAME;
	private $password 	= DB_PASSWORD;
	public static $instance;


	private function __construct()
	{
		# code...
	}

	/**
	 * getInstance: public function for providing database instance
	 */
	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new Database();
		}
		return self::$instance;
	}

	/**
	 * getConnection: get PDO connection
	 */
	public function getConnection()
	{
			
		try {
			$this->db = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database, $this->username, $this->password);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (Exception $e) {
			echo "Connection error: " . $e->getMessage();	
		}
		return $this->db;
	}

	/**
	 * query: execution of sql statement
	 */
	public function query($sql)
	{
		try {
			$db 	= $this->getConnection();
			$return = $db->query($sql);
			$result = $return->fetchAll(PDO::FETCH_OBJ); 
			$db 	= null;
			$return = null;
			return json_encode($result);
		} catch (Exception $e) {
			$error = $e->getMessage();
			return json_encode($error);
		}
	}


	/**
	 * Generic Select Fuction
	 */ 
	public function selectQuery($tableName="", $key="", $keyValue="", $orderBy="", $order="", $select="", $joinQuery="", $limitStart=0, $limitEnd=0, $extraQuery="" )
	{
		$sql = "SELECT ";
		if ($select == "") {
			$sql .= " * " ;
		} else {
			$sql .= " $select" ;
		}
		$sql .=" From $tableName" ;

		if($joinQuery <> "") {
			$sql .= " $joinQuery ";			
		}

		$sql .= " WHERE 1=1 ";

		if( $key <> "" &&  $keyValue <> "") {
			$sql .= " AND $key='$keyValue' ";
		}

		if ($extraQuery <> "") {
			$sql .= " $extraQuery";
		}

		if ($order <> "" && $orderBy <> "") {
			$sql .= " ORDER BY $orderBy $order ";
		}

		if ($limitStart > 0 && $limitEnd > 0) {
			$sql .= " LIMIT $limitStart, $limitEnd";
		}

		return $this->query($sql);
	}

	/**
	 * insert : Generic insert function
	 */
	public function insert($table, $elements)
	{
		$query = "INSERT IGNORE INTO $table (";
		$fields = "";
		$values = "";
		$isFirst = true;
		foreach ($elements as $key => $value) {
			if ($value != "now()") {
                $value = "'" . $value . "'";
            }

			if ($isFirst) {
				$fields .= $key;
				$values .= $value;
				$isFirst = false;
			} else {
				$fields .= "," . $key;
				$values .= "," . $value;
			}
		}
		$query .= $fields . ") VALUES (" . $values . ")";

		try {
			$db 	= $this->getConnection();
			$return = $db->query($query);
			$id 	= $db->lastInsertId();
			$db 	= null;
			$return = null;

			return $id;
		} catch (Exception $e) {
			$error = $e->getMessage();
			return json_encode($error);
		}
	}

}


?>