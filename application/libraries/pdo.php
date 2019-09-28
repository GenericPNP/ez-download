<?php
namespace application\Libraries;

class PDO Extends \PDO
{
    private $memcached;
    
    public function __construct($db_info) 
    {
        parent::__construct($db_info["dsn"], $db_info["username"], $db_info["password"]);
        parent::setAttribute(\PDO::ATTR_STATEMENT_CLASS, array("application\Libraries\PDOStatement", array($this)));
		
		$this->memcached = new Memcached();
		$this->memcached->addServer('mem1.domain.com', 11211, 33);
    }
    
    public function query($sql, $log_errors=1)
    {
        $query = parent::query($sql);
        
        if($log_errors==1)
        {
            if(parent::errorCode()>0) file_put_contents ("files/logs/db.log", str_repeat("-",50). "\nDate: ".date("d.m.Y, H:i:s")."\n\n".print_r(parent::errorInfo(),1). str_repeat("-",50). "\n\n", FILE_APPEND);
        }
        
        return $query;
    }
    
    public function cacheQuery($sql, $query_name, $expire)
    {
        $expire = (int)$expire*60;
        
        if($this->memcached->get($query_name) == null)
        {
           $caches [$query_name] = $expire;
           $results = self::query($sql)->fetchAll(PDO::FETCH_ASSOC);
           $this->memcached->set($query_name, $results, $expire);
           return $results;
        }
        else return $this->memcached->get($query_name);
    }
    
    public function getMemcached()
    {
        return $this->memcached;
    }
}

class PDOStatement Extends \PDOStatement
{
    private $pdo;
    
    private function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function fetch($fetch_style = PDO::FETCH_ASSOC, $cursor_orientation = null, $cursor_offset = null)
    {
        return parent::fetch($fetch_style);
    }

    public function fetchAll($fetch_style = PDO::FETCH_ASSOC, $column_index = null, $ctor_args = array()) {
        return parent::fetchAll($fetch_style);
    }
}
