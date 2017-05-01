<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjEmail
{
	private $attachments = array();

	private $emailRegExp = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i';

	private $eol = "\r\n";

	private $contentType = "text/plain";

	private $charset = "utf-8";
	
	private $charset8bit = array('UTF-8', 'SHIFT_JIS');
	
	private $headers = array();
	
	private $from = NULL;
	
	private $to = NULL;
	
	private $subject = NULL;
	
	private $part = NULL;
	
	private $uid = NULL;
	
	private $transport = 'mail'; //mail or smtp
	
	private $smtpHost = NULL;
	
	private $smtpPort = 25;
	
	private $smtpUser = NULL;
	
	private $smtpPass = NULL;

	public function __construct()
	{
		$this->uid = md5(uniqid(rand(), true));
	}
	
	public function attach($filename, $name=NULL, $mimetype='application/octet-stream')
	{
		$this->attachments[] = array(
			'filename' => $filename,
			'name' => !is_null($name) ? $name : basename($filename),
			'mimetype' => $mimetype
		);
		return $this;
	}
	
	public function detach($filename=NULL, $index=-1)
	{
		if (!empty($filename))
		{
			foreach ($this->attachments as $key => $attachment)
			{
				if ($attachment['filename'] == $filename)
				{
					$this->attachments[$key] = NULL;
					unset($this->attachments[$key]);
				}
			}
		}
		
		if ($index > -1 && array_key_exists($index, $this->attachments))
		{
			$this->attachments[$index] = NULL;
			unset($this->attachments[$index]);
		}
		
		if (is_null($filename) && $index == -1)
		{
			$this->attachments = array();
		}

		return $this;
	}
	
	private function getContentTransferEncoding()
	{
		$charset = strtoupper($this->charset);
		if (in_array($charset, $this->charset8bit))
		{
			return '8bit';
		}
		return '7bit';
	}
	
	public function getHeader($name)
	{
		foreach ($this->getHeaders() as $header)
		{
			list($key,) = explode(":", $header);
			if (strtolower($name) == strtolower(trim($key)))
			{
				return $header;
			}
		}
		return FALSE;
	}

	public function getHeaders()
	{
		return $this->headers;
	}
	
	private function getMessage($body)
	{
		$message = "";
		
		if (count($this->attachments) > 0)
		{
			$this->setHeader('Content-Type: multipart/mixed; boundary="PHP-mixed-'.$this->uid.'"');
		
			$message .= "--PHP-mixed-".$this->uid.$this->eol;
		    $message .= 'Content-Type: multipart/alternative; boundary="PHP-alt-'.$this->uid.'"'.$this->eol.$this->eol;
		
		    $message .= $this->getParts($body);
		    
			foreach ($this->attachments as $attachment)
			{
				if (!empty($attachment['filename']) && is_file($attachment['filename']))
				{
					ob_start();
					readfile($attachment['filename']);
					$fileContent = ob_get_contents();
					ob_end_clean();
					
					$content = chunk_split(base64_encode($fileContent));
					
					$message .= "--PHP-mixed-".$this->uid.$this->eol;
				    $message .= 'Content-Type: '.$attachment['mimetype'].'; name="'.$attachment['name'].'"'.$this->eol;
				    $message .= "Content-Transfer-Encoding: base64".$this->eol;
				    $message .= 'Content-Disposition: attachment; filename="'.$attachment['name'].'"'.$this->eol.$this->eol;
				    
				    $message .= $content.$this->eol;
				}
			}
			$message .= "--PHP-mixed-".$this->uid."--".$this->eol;
		} else {
			if (!empty($this->part))
			{
				$this->setHeader('Content-Type: multipart/mixed; boundary="PHP-mixed-'.$this->uid.'"');
				
				$message .= "--PHP-mixed-".$this->uid.$this->eol;
		    	$message .= 'Content-Type: multipart/alternative; boundary="PHP-alt-'.$this->uid.'"'.$this->eol.$this->eol;
		    
		    	$message .= $this->getParts($body);
				
				$message .= "--PHP-mixed-".$this->uid."--".$this->eol;
			} else {
				$message = $body;
			}
		}
		
		return $message;
	}
	
	private function getParts($body)
	{
		$message = "";
		
		if (!empty($this->part))
		{
			# Alternative part start
			$message .= "--PHP-alt-".$this->uid.$this->eol;
	    	$message .= "Content-type: ".$this->part['mimetype']."; charset=".(!empty($this->part['charset']) ? $this->part['charset'] : $this->charset).$this->eol;
	    	$message .= "Content-Transfer-Encoding: ".$this->getContentTransferEncoding().$this->eol.$this->eol;
	    
			$message .= $this->part['content'].$this->eol.$this->eol;
			# Alternative part end
		}
		
		# Default message start
		$message .= "--PHP-alt-".$this->uid.$this->eol;
    	$message .= "Content-type: ".$this->contentType."; charset=".$this->charset.$this->eol;
    	$message .= "Content-Transfer-Encoding: ".$this->getContentTransferEncoding().$this->eol.$this->eol;
    
		$message .= $body.$this->eol.$this->eol;
		# Default message end
		
		$message .= "--PHP-alt-".$this->uid."--".$this->eol.$this->eol;
				
		return $message;
	}

	public function send($body)
	{
		if (!preg_match($this->emailRegExp, $this->to))
		{
			return false;
		}

		if (!preg_match($this->emailRegExp, $this->from))
		{
			return false;
		}
		
		switch ($this->transport)
		{
			case 'mail':
				$message = $this->getMessage($body);
				
				$required = array(
					'MIME-Version' => '1.0',
					'Content-Type' => sprintf("%s; charset=%s", $this->contentType, $this->charset),
					'From' => $this->from,
					'Reply-To' => $this->from
				);
				
				foreach ($required as $key => $val)
				{
					if ($this->getHeader($key) === FALSE)
					{
						$this->setHeader(sprintf("%s: %s", $key, $val));
					}
				}
		
				return @mail($this->to, $this->subject, $message, join($this->eol, $this->getHeaders()));
				
				break;
			case 'smtp':
				require_once dirname(__FILE__) . '/pjPHPMailer.component.php';
				$mail = new pjPHPMailer();
				$mail->IsSMTP();
				try {
					$mail->Host = $this->smtpHost;
					$mail->Port = $this->smtpPort;
					if (!empty($this->smtpUser))
					{
						$mail->SMTPAuth = true;
						$mail->Username = $this->smtpUser;
						$mail->Password = $this->smtpPass;
					}
					$mail->AddAddress($this->to);
					$mail->SetFrom($this->from);
					$mail->AddReplyTo($this->from);
					$mail->Subject = $this->subject;
					$mail->MsgHTML($body);
					foreach ($this->attachments as $attachment)
					{
						if (!empty($attachment['filename']) && is_file($attachment['filename']))
						{
							$mail->AddAttachment($attachment['filename']);
						}
					}
					if (!$mail->Send())
					{
						//echo $mail->ErrorInfo;
						return false;
					} else {
						return true;
					}
				} catch (phpmailerException $e) {
					//echo $e->errorMessage();
					return false;
				} catch (Exception $e) {
					//echo $e->getMessage();
					return false;
				}
				break;
		}
	}

	public function addPart($content, $mimetype, $charset=NULL)
	{
		$this->part = compact('content', 'mimetype', 'charset');
		
		return $this;
	}
	
	public function setHeader($header)
	{
		if (strpos($header, ":") === FALSE)
		{
			return FALSE;
		}
		list($name,) = explode(":", $header);
		$name = strtolower(trim($name));
		foreach ($this->getHeaders() as $i => $h)
		{
			list($key,) = explode(":", $h);
			if ($name == strtolower(trim($key)))
			{
				$this->headers[$i] = NULL;
				unset($this->headers[$i]);
				break;
			}
		}
		array_push($this->headers, $header);
		$this->headers = array_values($this->headers);
		return $this;
	}

	public function setCharset($charset)
	{
		$this->charset = $charset;
		return $this;
	}

	public function setContentType($contentType)
	{
		if (!in_array($contentType, array('text/plain', 'text/html', 'multipart/mixed', 'multipart/alternative')))
		{
			return false;
		}
		$this->contentType = $contentType;
		return $this;
	}

	public function setEol($eol)
	{
		$this->eol = $eol;
		return $this;
	}

	public function setBcc($email)
	{
		$this->setHeader("Bcc: $email");
		return $this;
	}
	
	public function setCc($email)
	{
		$this->setHeader("Cc: $email");
		return $this;
	}
	
	public function setFrom($email)
	{
		$this->from = $email;
		return $this;
	}
	
	public function setReplyTo($email)
	{
		$this->setHeader("Reply-To: $email");
		return $this;
	}
	
	public function setReturnPath($email)
	{
		$this->setHeader("Return-Path: $email");
		return $this;
	}
	
	public function setSubject($subject)
	{
		$this->subject = !empty($subject) ? '=?UTF-8?B?'.base64_encode($subject).'?=' : $subject;
		return $this;
	}
	
	public function setSmtpHost($host)
	{
		$this->smtpHost = $host;
		return $this;
	}
	
	public function setSmtpPort($port)
	{
		$this->smtpPort = $port;
		return $this;
	}
	
	public function setSmtpUser($username)
	{
		$this->smtpUser = $username;
		return $this;
	}
	
	public function setSmtpPass($password)
	{
		$this->smtpPass = $password;
		return $this;
	}
	
	public function setTo($email)
	{
		$this->to = $email;
		return $this;
	}
	
	public function setTransport($transport)
	{
		if (in_array($transport, array('mail', 'smtp')))
		{
			$this->transport = $transport;
		}
		return $this;
	}
}
?>