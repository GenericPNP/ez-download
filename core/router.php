<?php
class Router
{
    private static $current_page;
    private static $page_struct;
    private static $is_router_loaded;
    
    private function __construct()
    {
        self::$current_page = self::getPage();
        self::$page_struct = explode("/", self::$current_page);
        self::$page_struct = array_values(array_filter(self::$page_struct, 'strlen'));
    }
    
    public static function init()
    {
        if(self::$is_router_loaded instanceof Router) return self::$is_router_loaded;
        
        else return self::$is_router_loaded = new Router;
    }
    
    public static function route()
    {
        self::loadAutoload();
        
        if(!self::getController())
        {
            try {
                self::loadDefaultPage();
            }
            catch(ConfigException $e) { return self::error404(); }
        }
        
        self::loadRewrites();
        self::loadPostedPage();
    }
    
    private static function loadDefaultPage()
    {
        require('application/config/routes.php');
            
        if(!is_array($routes["default_page"])) throw new ConfigException();
            
        else
        {
            $controller_cname = 'application\\Controllers\\'.$routes['default_page'][0];
            $controller_obj = new $controller_cname;
            $controller_obj->$routes['default_page'][1]();
        }
        exit;
    }
    
    private static function loadAutoload()
    {
        require_once("application/config/routes.php");
        require_once("application/config/autoload.php");
        
        $loader = \core\Loader::getInstance(true);
        
        foreach($autoload['libraries'] as $library)
        {
            $loader->library($library);
        }
        foreach(array_unique($autoload['controllers']) as $controller)
        {
           if((strtolower(self::getController()) != strtolower($controller)) && (self::getController() != null && $routes['default_page'][0] == 'users')) $loader->controller($controller);
        }
    }
    
    private static function loadRewrites()
    {
        require("application/config/routes.php");
        
        foreach ($routes as $rewrittenPage => $realPage)
        {
            if(is_array($realPage)) continue;
            
            if($rewrittenPage == str_replace(BASE_PATH, NULL, $_SERVER["REQUEST_URI"])) self::setPage($realPage);
            
            else if(preg_match_all('#\[(.*)\]#U', $rewrittenPage, $param_names))
            {
                $getRegex = preg_replace("#\[.*\]#U", "(.*)", $rewrittenPage);
                preg_match_all("#^\/?".$getRegex."$#", self::$current_page, $param_values); unset($param_values[0]);
                if(in_array(null, $param_values)) continue;
                
                else 
                {
                    $i = 0;
                    foreach($param_values as $p_value)
                    {
                        $realPage = str_replace('['.$param_names[1][$i].']', $param_names[1][$i].':'.$p_value[0], $realPage);
                        $i++;
                    }
                    self::setPage($realPage);
                }
            }
        }
    }
    
    private static function loadPostedPage()
    {
        if(self::getController() != null && $controller = self::getController())
        {
            $controller = "application\\Controllers\\".$controller;
            $controller = new $controller;
            
            if(!self::getMethod()) 
            {
                if(method_exists($controller, 'index')) 
                { 
                    $controller->index(); 
                }
            }
            else
            {
                $method = self::getMethod();
                
                if(!method_exists($controller, $method)) return self::error404();
                
                $method_data = new ReflectionMethod($controller, $method);
                
                if($method_data->isPublic() == true)
                {
                    if(!self::getParameters())
                    {
                        if($method_data->getNumberOfRequiredParameters() == 0) $controller->$method(); else self::error404();
                    }
                    else
                    {
                        $parametersToSet = self::getParameters();
                        $sortParams = array();
                        
                        foreach($method_data->getParameters() as $params)
                        {
                            if(!$params->isOptional() && !isset($parametersToSet[$params->getName()])) return self::error404 ();
                            
                            if($params->isOptional() && !isset($parametersToSet[$params->getName()])) $sortParams[] = $params->getDefaultValue();
                            else $sortParams[] = $parametersToSet[$params->getName()];
                        }
                        $method_data->invokeArgs($controller, $sortParams);
                    }
                } else return self::error404();
            }
        }
    }
    
    public static function error404()
    {
        header("HTTP/1.0 404 Not Found");
        die(file_get_contents('application/errors/404.html'));
        exit;
    }
    
    public static function autoload($className)
    {
        if(class_exists($className)) return true;
        
        else
        {    
            $className = strtolower(str_replace('\\', '/', $className));
			if(!file_exists($className.'.php')) return self::error404();;
            
            require_once $className .'.php';
        }
    }
    
    private static function getController()
    {
        if(isset(self::$page_struct[0])) 
            return self::$page_struct[0];
    }
    
    private static function getMethod()
    {
        if(isset(self::$page_struct[1])) 
            return self::$page_struct[1];
    }
    
    private static function getParameters()
    {
        $parameters = array();
        
        foreach(self::$page_struct as $place => $args)
        {
            if($place > 1 && strstr($args, ':')) 
            {
                $parameter = explode(":", $args);
                $parameters[$parameter[0]] = $parameter[1];
                $_GET[$parameter[0]] = $parameter[1];
            }
        }
        
        return $parameters;
    }
    
    public static function getPage()
    {
        $rpath = BASE_PATH == "/" ? NULL : BASE_PATH;
		
        if(self::$current_page == null) self::$current_page = (
                 str_replace('?'.$_SERVER["QUERY_STRING"], NULL, 
                         str_replace($rpath, NULL, $_SERVER["REQUEST_URI"])));
        
        return self::$current_page;
    }
    
    private static function setPage($page)
    {
        self::$current_page = $page;
        self::$page_struct = explode("/", self::$current_page);
        self::$page_struct = array_filter(self::$page_struct, 'strlen');
    }
}
class ConfigException Extends Exception {}
?>