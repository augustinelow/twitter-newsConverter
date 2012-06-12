<?php
require_once("accessConfig.php");

class DB_Class {
	private $username=Config::db_userName;
	private $password=Config::db_password;
	private $dbname=Config::db_dbname;
	
     private $db;
     ///////////////////////////
     function DB_Class() {
          $this->db = MYSQL_CONNECT ('localhost', $this->username, $this->password)
           or DIE ("Unable to connect to Database Server");
 
          MYSQL_SELECT_DB ($this->dbname, $this->db) or DIE ("Could not select database");
     }
 
     function query($sql) {
          $result = MYSQL_QUERY ($sql, $this->db) or DIE ("Invalid query: " . MYSQL_ERROR());
          RETURN $result;
     }
     ///////////////////////////
     function fetch($sql) {
          $data = ARRAY();
          $result = $this->query($sql);
 
          WHILE($row = MYSQL_FETCH_ASSOC($result)) {
               $data[] = $row;
          }
               RETURN $data;
     }
     ///////////////////////////
    function getone($sql) {
		$result = $this->query($sql);
		return mysql_fetch_row($result);
	}
     ///////////////////////////
}
?>