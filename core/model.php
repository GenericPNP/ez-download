<?php
namespace core;

class Model
{
    public static $loader = array();
    
    private static $conn;
    protected $db;
    
    public function __construct() 
    {
        if(!isset(self::$loader['load'])) $this->load = new Loader('model');
        
        if(!is_object(self::$conn))
            $this->dbInit();
        
        $this->db = self::$conn;
        $this->db->query("SET NAMES UTF8");
    }
    
    private function dbInit()
    {
        $db = $this->dbInfo();
        
        if($db['type'] == 'pdo')
        {
            $this->load->library ('pdo', array('dsn'=>'mysql:dbname='.$db["db_name"].';host='.$db["host"], 'username' => $db["username"], 'password' => $db["password"]));
            self::$conn =& $this->pdo;
        }
        else if($db['type'] == 'mysqli')
            self::$conn = new mysqli($db['host'], $db['username'], $db['password'], $db['db_name']); 
    }
    
    private function dbInfo()
    {
        require_once('application/config/database.php');
        
        if(is_array($database)) return $database; else return;
    }
    
    public function __set($name, $value)
    {
        if($name == 'load' && isset(self::$loader['load'])) die('Unable to override "load" property');
        
        self::$loader[$name] = $value;
    }
    
    public function __get($name) 
    {
        return self::$loader[$name];
    }
}
?>
