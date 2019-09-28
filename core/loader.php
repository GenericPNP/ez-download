<?php
namespace core;

class Loader
{
    private $called_by = 'model';
    public static $instance = null;
    private static $loaded_models;
    private static $loaded_libraries;
    
    public function __construct($child_class)
    {
        if($child_class != 'controller')
            $this->called_by = $child_class;
        else if($child_class === true)
            $this->called_by = true;
        else
            $this->called_by = 'controller';
    }
    
    public static function getInstance()
    {
        if(!is_object(self::$instance)) 
            return self::$instance = new Loader(true);
        else 
            return self::$instance;
    }
    
    public function view($view_name, $data = null, $ext = 'php')
    {
        if($this->called_by == 'model') die("Unable to call views from model");
        
        if(file_exists('application/views/'.$view_name.'.'.$ext))
        {
            if(is_array($data)) extract($data);
            require_once('application/views/'.$view_name.'.'.$ext);
        }
    }
    
    public function model($model_name)
    {
        if(isset(self::$loaded_models[$model_name])) return self::$loaded_models[$model_name];
        
        else
        {
            if(file_exists('application/models/'.$model_name.'.php'))
            {
                $model_name_wns = 'application\Models\\'.$model_name;
                return self::$loaded_models[$model_name] = new $model_name_wns();
            }
        }
    }
    
    public function library($lib_name, $fc_param = null)
    {
        // Така, значи може да изглежда малко странно и объркано, за това малко обяснение (на български, че ще Ви объркам, хаха)
        // ( WTF is Model/Controller::$loader )
        // 
        // $loader и в Model и в Controller, държат всички недекларирани свойствa (принципo - библиотеки, извикани от този клас)
        // (__set/__get magic methods - registry)
        // 
        // Има и другата идея е да се забрани редекларирането на $load (свойство, което държи този клас), както и да е
        // Понеже библиотеките се зареждат посредством този метод и се викат $this->LIBNAME->... (like CI)
        // В случая LIBNAME ще е недекларирана и ще се извика __get от разширения клас, той ще върне
        // той ще върне loader[LIBNAME], който вече ще съдържа класа на библиотеката подадена му оттук
        //
        
        $lib_name_wns = 'application\Libraries\\'.$lib_name;
        
        if($this->called_by === true)
        {
            if(isset(self::$loaded_libraries[$lib_name]) && self::$loaded_libraries[$lib_name] instanceof $lib_name_wns)
            {
                Model::$loader[$lib_name] = self::$loaded_libraries[$lib_name] ;
                Controller::$loader[$lib_name] = self::$loaded_libraries[$lib_name] ;
            }
            else
            {
                $library_obj = new $lib_name_wns($fc_param);
                
                Model::$loader[$lib_name] = $library_obj;
                Controller::$loader[$lib_name] = $library_obj;
                self ::$loaded_libraries[$lib_name] = $library_obj;
            }
        }
        
        else if($this->called_by == 'model') 
        {
            if(isset(self::$loaded_libraries[$lib_name]) && self::$loaded_libraries[$lib_name] instanceof $lib_name_wns)
                Model::$loader[$lib_name] = self::$loaded_libraries[$libname];
        
            else
            {
                $library_obj = new $lib_name_wns($fc_param);
                Model::$loader[$lib_name] = $library_obj;
                self ::$loaded_libraries[$lib_name] = $library_obj;
            }
        }
        else if($this->called_by == 'controller')
        {
            if(isset(self::$loaded_libraries[$lib_name]) && self::$loaded_libraries[$lib_name] instanceof $lib_name_wns)
                Controller::$loader[$lib_name] = self::$loaded_libraries[$libname];
        
            else
            {
                $library_obj = new $lib_name_wns($fc_param);
                
                Controller::$loader[$lib_name] = $library_obj;
                self ::$loaded_libraries[$lib_name] = $library_obj;
            }
        }
    }    
    
    public function controller($controller_name)
    {
        $controller_name = 'application\Controllers\\'.$controller_name;
        
        return new $controller_name();
    }
}
?>