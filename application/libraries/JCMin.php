<?php
namespace application\Libraries;

class JCMin 
{
    private static $from_dir = "application/views/";
    private static $cmin_file;
    
	public function __construct()
	{
	}
	
    public function doMinify($cmin)
    {
        $template_dir =  self::$from_dir . current(explode("/", $cmin));
        @$fileFormat = end(explode(".", $cmin));
        @$fileNameOnly = str_replace(".".end(explode(".",basename($cmin))), NULL,  basename($cmin));                
        self::$cmin_file = $cmin;
        if(!is_dir($template_dir ."/cache"))
        {
            mkdir($template_dir . "/cache", 0777);
        }
        if(filemtime(self::$from_dir . self::$cmin_file)+10 > @filemtime($template_dir."/cache/".sha1($fileNameOnly).".".$fileFormat) . "<hr/>")
        {
            if($this->minifyFile() !== false)
            {
                file_put_contents($template_dir . "/cache/".sha1($fileNameOnly) . ".".$fileFormat, $this->minifyFile());
            }
        }
       
    }
    
    private function minifyFile()
    {
        if(file_exists(self::$from_dir . self::$cmin_file))
        {
            if(@end(explode(".", self::$cmin_file)) == "js")
            {
                $jsmin = JSMin::minify(file_get_contents(self::$from_dir . self::$cmin_file));
                return $jsmin;
            }
            else
            {
                $contents = file_get_contents(self::$from_dir . self::$cmin_file);
                $contents = preg_replace("#(.+) {#imsU", "$1{", $contents);
                $contents = preg_replace("#(.+): #imsU", "$1:", $contents);
                $contents = str_replace("\n", NULL, $contents);
                $contents = str_replace("\r\n", NULL, $contents);
                $contents = str_replace("   ", NULL, $contents);
                $contents = preg_replace("#(\s\s)+#", NULL, $contents);
                $contents = preg_replace("#(.+){ #imsU", "$1{", $contents);
                $contents = preg_replace("#(.+): #imsU", "$1:", $contents);
                $contents = preg_replace("#;\n}#imsU", NULL, $contents);
                return $contents;
            }
        }
        else
        {
            return FALSE;
        }
    }
    
}

?>
