<?php
/**
 * не се използва
 * @deprecated
 */
class Database 
{

    private $host, $user, $password, $database;

    public function __construct($host, $user, $password, $database, $encoding = "UTF8") 
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->connect();
        $this->query("SET NAMES " . $encoding);
    }
    
    private function connect() 
    {
        $connection = mysql_connect($this->host, $this->user, $this->password) or die ("Connection failed");
        mysql_select_db($this->database, $connection) or die ("Database not found");
        
    }

    public function query($sql) 
    {
        $query = mysql_query($sql) or die (mysql_error());
        return $query;
    }
    
    public function fetchAssoc($query) 
    {
        return mysql_fetch_assoc($query);
    }
    
    public function numRows($query) 
    {
        return mysql_num_rows($query);
    } 
}

?>
