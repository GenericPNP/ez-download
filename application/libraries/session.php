<?php
namespace application\Libraries;

class Session 
{
    private $session_id;
    
    public function __construct() 
    {
        if(!isset($_SESSION)) $this->sessionInit();
    }
    
    public function sessionInit() 
    { 
        session_start();
    }
     
    public function setSession($session_name, $value, $isSCookie = false) 
    {
        $_SESSION[$session_name]=$value;
    }
    
    // <--
    public function updateDDimensional($skey1, $skey2, $value)
    {
        $_SESSION[$skey1][$skey2] = $value;
    }
    // -->
    
    public function getSession($session_name, $session_skey = NULL)
    {
        if($session_skey === NULL)
        {
            return @$_SESSION[$session_name];
        }
        else
        {
            return @$_SESSION[$session_name][$session_skey];
        }
    }
    
    protected function resetSessionId()
    {
        $this->session_id = session_regenerate_id();
        return $this->session_id;
    }
    
    private function startSessionCookie()
    {
        setcookie(session_name(), session_id(), time()+360000*365, "/");
    }
    
    public function setSessionId($session_id)
    {
        $this->session_id = $session_id;
        session_id($session_id);
    }
    
    public function destroySession() 
    {
        session_destroy();
    }
	
    public function unsetSession($session_name)
    {
        unset($_SESSION[$session_name]);
    }
    
}
?>
