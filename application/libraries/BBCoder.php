<?php
namespace application\Libraries;
class BBCoder 
{
    private $content;
    
    public function __construct() {}
    
    public function setContent($content)
    {
        $this->content = ($content);
        $this->bbCode();
        $this->colorizePHP();
        return ($this->content);
    }
    
    private function colorizePHP()
    {
        if(!function_exists("br2nl"))
        {
            function br2nl($string)
            {
                return str_replace("<br />","",$string);
            }
        }
        $this->content = str_replace("]\n", "]", $this->content);
        $match = array('#\[code\](.+)\[\/code\]#ise');
        $replace = array("highlight_string(htmlspecialchars_decode(br2nl(stripslashes('$1'))), true)");
        $this->content = preg_replace($match, $replace, $this->content); 
    }
    
    private function bbCode()
    {
        $this->content = preg_replace("#\[quote\=(.+)\]#ismU", "<div style=\"margin-bottom:-19px;\"><span class=\"fs9p\"><div style='margin-top:3px;'>$1 написа:</div></span><div class=\"quote\">", $this->content);
        $this->content = str_replace("[/quote]", "</div></div><div class=\"clearer\">&nbsp;</div>", $this->content);
        $this->content = preg_replace("#\[b\](.+)\[/b\]#ismU", "<b>$1</b>", $this->content);
        $this->content = preg_replace("#\[i\](.+)\[/i\]#ismU", "<i>$1</i>", $this->content);
        $this->content = preg_replace("#\[u\](.+)\[/u\]#ismU", "<u>$1</u>", $this->content);
        $this->content = preg_replace("#\[i\](.+)\[/i\]#ismU", "<i>$1</i>", $this->content);        
        $this->content = preg_replace("/((http|ftp|https)\:\/\/([^\s]+))/i", " <a rel=\"nofollow\" target=\"_blank\" href=\"$1\">$1</a>",($this->content));
        $this->content = nl2br($this->content);
    }
}

?>
