<?php

class Email {

    private $_from_name;
    private $_from_email;
    private $_recepients;
    private $_subject;
    private $_content;
    private $_headers;
    
    function __construct($from_name, $from_email, $rcpt, $subj, $content, $headers = null) {
        $this->_from_name = $from_name;
        $this->_from_email = $from_email;
        $this->_recepients = $rcpt;
        $this->_subject = $subj;
        $this->_content = $content;
        
        if( ! is_null($headers)){
        	$this->_headers = $headers;
        }else{
        	$this->_headers = 'MIME-Version: 1.0'."\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
            
            $headers .= 'From: '.$this->_from_name.' '.$this->_from_email."\r\n";
            $headers .= 'Return-Path: '.$this->_from_name.' '.$this->_from_email."\r\n";
            $headers .= 'Reply-To: '.$this->_from_name.' '.$this->_from_email."\r\n";
            
            $header .= 'X-Priority: 2'."\r\n"; 
            $header .= 'X-MSMail-Priority: High'."\r\n"; 
            $header .= 'X-Mailer: PHP/'.phpversion();  
        }
    }
    
    public function send(){
    	return mail($this->_recepients, $this->_subject, wordwrap($this->_content, 70), $this->_headers);
    }
}
?>