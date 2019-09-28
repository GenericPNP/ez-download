<?php
namespace application\Libraries;

class Captcha 
{   
    private $session;
    
    public function __construct($session)
    {
        $this->session = $session;
    }
    
    public function makeCaptcha($bgcolor, $text_color, $font_size, $font) 
    {
        header("Content-type: image/png");
        $image = imagecreatetruecolor(100, 35);
        $bgcolor = $this->hex2rgb($bgcolor);
        $text_color = $this->hex2rgb($text_color);
        $bgcolor = imagecolorallocate($image, $bgcolor[0], $bgcolor[1], $bgcolor[2]);
        imagefill($image, 0, 0, $bgcolor);
        $text = $this->generateCode();
        $text_color = imagecolorallocate($image, $text_color[0], $text_color[1], $text_color[2]);
        imagettftext($image, $font_size, 0, 15, 26, $text_color, $font, $text);
        imagepng($image);
        imagedestroy($image);
    }
    
    private function generateCode() 
    {
        $captcha = isset($captcha);
        $arr=array(1,2,3,4,5,6,7,8,9,1,2,3,'a','b','c','d','e','e','f','g','z');
        for($i=0;$i<5;$i++) 
	{
            $captcha .= $arr[rand(0, count($arr)-1)];
        }
        $this->session->setSession('captcha', $captcha);
        return $captcha;
    }    
    
    private function hex2rgb($hex)
    {
        if (0 === strpos($hex, '#')) $hex = substr($hex, 1);
        
        else if (0 === strpos($hex, '&H')) $hex = substr($hex, 2);
        
        $cutpoint = ceil(strlen($hex) / 2)-1;
        $rgb = explode(':', wordwrap($hex, $cutpoint, ':', $cutpoint), 3);

        $rgb[0] = (isset($rgb[0]) ? hexdec($rgb[0]) : 0);
        $rgb[1] = (isset($rgb[1]) ? hexdec($rgb[1]) : 0);
        $rgb[2] = (isset($rgb[2]) ? hexdec($rgb[2]) : 0);

        return $rgb;
    }
}

?>
