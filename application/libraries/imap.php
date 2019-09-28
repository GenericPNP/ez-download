<?php
namespace application\Libraries;

class Imap 
{
    private $mailbox;
	private $user;
	
	public function __construct()
    {
    }
	
	public function openIMAP($server, $user, $password)
    {
		$this->user = $user;
        if(!isset($this->mailbox)) $this->mailbox = imap_open("{{$server}:143/novalidate-cert}", $user, $password);
    }
    
    public function getEmails()
    {
        $MC = imap_check($this->mailbox);
        $result = imap_fetch_overview($this->mailbox,"1:".$MC->Nmsgs,0);
        return $result;
    }
    
    public function read_mail($msgno)
    {
        $no = $msgno;
        $bodyText = imap_fetchbody($this->mailbox,$no,1.2);
        if(!strlen($bodyText)>0)
		{
            $bodyText = imap_fetchbody($this->mailbox,$no,1);
            $toIMAPB64 = imap_base64($bodyText);
            if((bool)$toIMAPB64 != FALSE) $bodyText = iconv ('windows-1251', 'utf-8', $toIMAPB64);
        }
        $subject = imap_headerinfo($this->mailbox, $no);
        
        return array_merge(array('body' => $bodyText),(array)$subject);
    }
    
    public function deleteMail($id)
    {
        imap_delete($this->mailbox, $id);
        imap_expunge($this->mailbox);
		return true;
    }
    
    private function sendMail($to, $subject, $message)
    {
        if(preg_match("#[a-z0-9_]+@[a-z0-9_\-]+\.[a-z]{2,4}#ismU", $to)
				&& mb_strlen($subject, 'UTF-8') > 3 
					&& mb_strlen($message, "UTF-8") > 3)
        {     
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .= $this->user.'@schooldocs-bg.com' . "\r\n";
			$mail = mail($to, $subject, $message, $headers);
            if($mail) return true;
			
			return;
        }
        return FALSE;
    }
    
}
?>
