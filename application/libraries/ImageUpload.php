<?php
namespace application\Libraries;

class ImageUpload
{
    private $temporary, $upload_name, $save_name, $upload_path;
    private $extension, $file_size, $file_contents, $maxFileSize, $isResized;
    
    public function toUpload($temporary_file, $upload_name, $save_name, $upload_path)
    {
        $this->temporary = $temporary_file;
        $this->upload_name = $upload_name;
        $this->save_name = $save_name;
        $this->upload_path = $upload_path;
        
        $this->file_contents = @file_get_contents($this->temporary);
        $this->file_size = filesize($this->temporary)/1024;
        
        $this->extension = explode(".", $this->upload_name);
        $this->extension = end($this->extension);
        $this->extension = $this->extension == "jpg" ? "jpeg" : $this->extension;
    }
    
    public function saveImage() 
    {
        if($this->isValidImage() !== TRUE) return FALSE; 
        
        else
        {
            if(!$this->isResized) 
                move_uploaded_file($this->temporary, $this->upload_path.'/'.$this->save_name.'.'.$this->extension);
            else 
                imagepng($this->temporary, $this->upload_path.'/'.$this->save_name.'.png');
        }
    }
    
    private function isValidImage()
    {
        $allowed_formats = array("png", "jpeg", "gif");
        
        if(!in_array($this->extension, $allowed_formats))
            return FALSE;
        if(strstr($this->file_contents, 'php'))
            return FALSE;
        if((int)$this->maxFileSize > 0 && ($this->file_size > $this->maxFileSize))
            return FALSE;
        
        return TRUE;
    }
    
    private function getFunctionByExt()
    {
        return "imagecreatefrom".$this->extension;
    }
    
    public function resize($maxWidth)
    {
        if($this->isValidImage() !== TRUE) return FALSE;
        
        else
        {
            list($width, $height) = getimagesize($this->temporary);
            $newHeight = $height*($maxWidth/$width);
            $function  = $this->getFunctionByExt();
            $Image = imagecreate($maxWidth, $newHeight);
            $colourBlack = imagecolorallocate($Image, 0, 0, 0);
            imagecolortransparent($Image, $colourBlack);
            $Source = $function($this->temporary);
            $resized_rs = imagecopyresized($Image, $Source, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
            $this->temporary = $Image;
            $this->isResized = true;
        }
    }
    
    public function setUploadMaxSize($kb)
    {
        if((int)$kb > 0) $this->maxFileSize = $kb;
    }
}
?>