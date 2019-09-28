<?php
namespace application\Libraries;

class Paypal
{
    var $response;
    var $pp_data = array(); 
    var $fields = array();           
 
    public function __construct()
    {
        $this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
        $this->response = '';
        $this->add_field('rm','2');           
        $this->add_field('cmd','_xclick');    
    }
   
    public function add_field($field, $value) 
    {
        $this->fields[$field] = $value;
    }
   
    public function submit_paypal_post() 
    {   
        echo "<form method=\"post\" name=\"paypal_form\" id='pp_form' ";
        echo "action=\"".$this->paypal_url."\">\n";
        foreach ($this->fields as $name => $value) {
        echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
        }
        echo "</form>\n";
    }   
   
    public function validate_ipn() 
    {	   
        $url_parsed = parse_url($this->paypal_url);        
	  
        $post_string = '';    
        foreach ($_POST as $field=>$value) { 
            $this->pp_data[$field] = $value;
            $post_string .= $field.'='.urlencode(stripslashes($value)).'&'; 
        }
        $post_string.="cmd=_notify-validate"; 
      
        $fp = fsockopen($url_parsed["host"],"80",$err_num,$err_str,30); 
        if(!$fp) 
        {
            return false;
        }
      
        else 
        { 
			//
            fputs($fp, "POST ".$url_parsed["path"]." HTTP/1.1\r\n"); 
            fputs($fp, "Host: ".$url_parsed["host"]."\r\n"); 
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
            fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
            fputs($fp, "Connection: close\r\n\r\n"); 
            fputs($fp, $post_string . "\r\n\r\n"); 
         
            while(!feof($fp)) { 
                $this->response .= fgets($fp, 1024); 
            } 
            fclose($fp);
        }
        if (strstr($this->response, "VERIFIED")) return TRUE;
		
        else 
            return FALSE; 
    }
   
}     
?>