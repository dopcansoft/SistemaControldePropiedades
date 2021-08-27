<?
class Mailer extends Model{
	public $header;
	public $to;
	public $toName;
	public $from;
	public $reply;
	public $replyName;
	public $cc;
	public $bcc;
	public $subject;
	public $message;
	
	public function __construct($data=null, $prefix=null, $separator='.'){
		if(isset($data)){
			$this->unserialize($data, $prefix, $separator);	
		}else{
			$this->unserialize(array());
		}
	}

	public function unserialize($data, $prefix=null, $separator='.'){
		$unserialized = false;
		try{
			$this->to = $this->val("to", $data, $prefix, $separator);
			$this->toName = $this->val("toName", $data, $prefix, $separator);
			$this->from = $this->val("from", $data, $prefix, $separator);
			$this->reply = $this->val("reply", $data, $prefix, $separator);
			$this->replyName = $this->val("replyName", $data, $prefix, $separator);
			$this->cc = $this->val("cc", $data, $prefix, $separator);
			$this->bcc = $this->val("bcc", $data, $prefix, $separator);
			$this->subject = $this->val("subject", $data, $prefix, $separator);
			$this->message = $this->val("message", $data, $prefix, $separator);
			$unserialized = true;
		}catch(Exception $e){
			$unserialized = false;
		}
		return $unserialized;	
	}			

	function prepare(Logger $log){
		$success = false;
		if($this->to!=null && trim($this->to)!='' && $this->toName!=null && trim($this->toName)!='' && $this->reply!=null && trim($this->reply)!='' && $this->replyName!=null && trim($this->replyName)!=''){
			$cabeceras  = "MIME-Version: 1.0\r\n";
			$cabeceras .= "Content-type: text/html; charset=UTF-8\r\n";
			$cabeceras .= "To: ".$this->toName." <".$this->to.">\r\n";
			$cabeceras .= "From: ".$this->replyName." <".$this->reply.">\r\n";
			$this->header = $cabeceras;
			$success = true;
		}else{
			$log->error('Faltan parametros de configuración');
		}		
		return $success;
	}

	public function send(Logger $log){
		$success=false;
		if($this->to!=null && trim($this->to)!='' && 
			$this->subject!=null && trim($this->subject)!='' && 
			$this->message!=null && trim($this->message)!='' && 
			$this->header!=null && trim($this->header)!=''){
			try{
				$success=mail($this->to, $this->subject, $this->message, $this->header);			
			}catch(Exception $e){
				$log->error('Exception: '.$e->getMessage());
				$success=false;
			}
		}else{
			$log->error('Faltan parametros de configuración');
		}
		return $success;
	}
}
?>