<?php
namespace core;

class Controller
{
    public static $loader = array();
    
    public function __construct() 
    {
        if(!isset(self::$loader['load'])) $this->load = new Loader('controller');    
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