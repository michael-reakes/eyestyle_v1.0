<?php
/*
 * smtp.php
 *
 * @(#) $Header: /home/mlemos/cvsroot/smtp/smtp.php,v 1.37 2004/10/05 04:00:46 mlemos Exp $
 *
 */

class utils_smtp
{
	var $user="";
	var $realm="";
	var $password="";
	var $workstation="";
	var $authentication_mechanism="";
	var $host_name="";
	var $host_port=25;
	var $localhost="";
	var $timeout=0;
	var $data_timeout=0;
	var $direct_delivery=0;
	var $error="";
	var $debug=0;
	var $html_debug=0;
	var $esmtp=1;
	var $esmtp_host="";
	var $esmtp_extensions=array();
	var $maximum_piped_recipients=100;
	var $exclude_address="";
	var $getmxrr="GetMXRR";
	var $pop3_auth_host="";
	var $pop3_auth_port=110;

	/* private variables - DO NOT ACCESS */

	var $state="disconnected";
	var $connection=0;
	var $pending_recipients=0;
	var $next_token="";
	var $direct_sender="";
	var $connected_domain="";
	var $result_code;

	/* Private methods - DO NOT CALL */

	Function tokenize($string,$separator="")
	{
		if(!strcmp($separator,""))
		{
			$separator=$string;
			$string=$this->next_token;
		}
		for($character=0;$character<strlen($separator);$character++)
		{
			if(GetType($position=strpos($string,$separator[$character]))=="integer")
				$found=(IsSet($found) ? min($found,$position) : $position);
		}
		if(IsSet($found))
		{
			$this->next_token=substr($string,$found+1);
			return(substr($string,0,$found));
		}
		else
		{
			$this->next_token="";
			return($string);
		}
	}

	Function output_debug($message)
	{
		$message.="\n";
		if($this->html_debug)
			$message=str_replace("\n","<br />\n",HtmlEntities($message));
		echo $message;
		flush();
	}

	Function set_data_access_error($error)
	{
		$this->error=$error;
		if(function_exists("socket_get_status"))
		{
			$status=socket_get_status($this->connection);
			if($status["timed_out"])
				$this->error.=": data access time out";
			elseif($status["eof"])
				$this->error.=": the server disconnected";
		}
	}

	Function get_line()
	{
		for($line="";;)
		{
			if(feof($this->connection))
			{
				$this->error="reached the end of data while reading from the SMTP server conection";
				return("");
			}
			if(GetType($data=fgets($this->connection,100))!="string"
			|| strlen($data)==0)
			{
				$this->set_data_access_error("it was not possible to read line from the SMTP server");
				return("");
			}
			$line.=$data;
			$length=strlen($line);
			if($length>=2
			&& substr($line,$length-2,2)=="\r\n")
			{
				$line=substr($line,0,$length-2);
				if($this->debug)
					$this->output_debug("S $line");
				return($line);
			}
		}
	}

	Function put_line($line)
	{
		if($this->debug)
			$this->output_debug("C $line");
		if(!fputs($this->connection,"$line\r\n"))
		{
			$this->set_data_access_error("it was not possible to send a line to the SMTP server");
			return(0);
		}
		return(1);
	}

	Function put_data(&$data)
	{
		if(strlen($data))
		{
			if($this->debug)
				$this->output_debug("C $data");
			if(!fputs($this->connection,$data))
			{
				$this->set_data_access_error("it was not possible to send data to the SMTP server");
				return(0);
			}
		}
		return(1);
	}

	Function verify_result_lines($code,&$responses)
	{
		$responses=array();
		Unset($this->result_code);
		while(strlen($line=$this->get_line($this->connection)))
		{
			if(IsSet($this->result_code))
			{
				if(strcmp($this->tokenize($line," -"),$this->result_code))
				{
					$this->error=$line;
					return(0);
				}
			}
			else
			{
				$this->result_code=$this->tokenize($line," -");
				if(GetType($code)=="array")
				{
					for($codes=0;$codes<count($code) && strcmp($this->result_code,$code[$codes]);$codes++);
					if($codes>=count($code))
					{
						$this->error=$line;
						return(0);
					}
				}
				else
				{
					if(strcmp($this->result_code,$code))
					{
						$this->error=$line;
						return(0);
					}
				}
			}
			$responses[]=$this->tokenize("");
			if(!strcmp($this->result_code,$this->tokenize($line," ")))
				return(1);
		}
		return(-1);
	}

	Function flush_recipients()
	{
		if($this->pending_sender)
		{
			if($this->verify_result_lines("250",$responses)<=0)
				return(0);
			$this->pending_sender=0;
		}
		for(;$this->pending_recipients;$this->pending_recipients--)
		{
			if($this->verify_result_lines(array("250","251"),$responses)<=0)
				return(0);
		}
		return(1);
	}

	Function connect_to_host($domain, $port, $resolve_message)
	{
		if(ereg('^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$',$domain))
			$ip=$domain;
		else
		{
			if($this->debug)
				$this->output_debug($resolve_message);
			if(!strcmp($ip=@gethostbyname($domain),$domain))
				return("could not resolve host \"".$domain."\"");
		}
		if(strlen($this->exclude_address)
		&& !strcmp(@gethostbyname($this->exclude_address),$ip))
			return("domain \"".$domain."\" resolved to an address excluded to be valid");
		if($this->debug)
			$this->output_debug("connecting to host address \"".$ip."\"...");
		if(($this->connection=($this->timeout ? @fsockopen($ip,$port,$errno,$error,$this->timeout) : @fsockopen($ip,$port))))
			return("");
		$error=($this->timeout ? strval($error) : "??");
		switch($error)
		{
			case "-3":
				return("-3 socket could not be created");
			case "-4":
				return("-4 dns lookup on hostname \"".$domain."\" failed");
			case "-5":
				return("-5 connection refused or timed out");
			case "-6":
				return("-6 fdopen() call failed");
			case "-7":
				return("-7 setvbuf() call failed");
		}
		return("could not connect to the host \"".$domain."\": ".$error);
	}

	Function sasl_authenticate($mechanisms, $credentials, &$authenticated, &$mechanism)
	{
		$authenticated=0;
		if(!function_exists("class_exists")
		|| !class_exists("sasl_client_class"))
		{
			$this->error="it is not possible to authenticate using the specified mechanism because the SASL library class is not loaded";
			return(0);
		}
		$sasl=new sasl_client_class;
		$sasl->set_credential("user",$credentials["user"]);
		$sasl->set_credential("password",$credentials["password"]);
		if(IsSet($credentials["realm"]))
			$sasl->set_credential("realm",$credentials["realm"]);
		if(IsSet($credentials["workstation"]))
			$sasl->set_credential("workstation",$credentials["workstation"]);
		if(IsSet($credentials["mode"]))
			$sasl->set_credential("mode",$credentials["mode"]);
		do
		{
			$status=$sasl->start($mechanisms,$message,$interactions);
		}
		while($status==SASL_INTERACT);
		switch($status)
		{
			case SASL_CONTINUE:
				break;
			case SASL_NOMECH:
				if(strlen($this->authentication_mechanism))
				{
					$this->error="authenticated mechanism ".$this->authentication_mechanism." may not be used: ".$sasl->error;
					return(0);
				}
				break;
			default:
				$this->error="Could not start the SASL authentication client: ".$sasl->error;
				return(0);
		}
		if(strlen($mechanism=$sasl->mechanism))
		{
			if($this->put_line("AUTH ".$sasl->mechanism.(IsSet($message) ? " ".base64_encode($message) : ""))==0)
			{
				$this->error="Could not send the AUTH command";
				return(0);
			}
			if(!$this->verify_result_lines(array("235","334"),$responses))
				return(0);
			switch($this->result_code)
			{
				case "235":
					$response="";
					$authenticated=1;
					break;
				case "334":
					$response=base64_decode($responses[0]);
					break;
				default:
					$this->error="Authentication error: ".$responses[0];
					return(0);
			}
			for(;!$authenticated;)
			{
				do
				{
					$status=$sasl->step($response,$message,$interactions);
				}
				while($status==SASL_INTERACT);
				switch($status)
				{
					case SASL_CONTINUE:
						if($this->put_line(base64_encode($message))==0)
						{
							$this->error="Could not send the authentication step message";
							return(0);
						}
						if(!$this->verify_result_lines(array("235","334"),$responses))
							return(0);
						switch($this->result_code)
						{
							case "235":
								$response="";
								$authenticated=1;
								break;
							case "334":
								$response=base64_decode($responses[0]);
								break;
							default:
								$this->error="Authentication error: ".$responses[0];
								return(0);
						}
						break;
					default:
						$this->error="Could not process the SASL authentication step: ".$sasl->error;
						return(0);
				}
			}
		}
		return(1);
	}

	/* Public methods */

	Function connect($domain="")
	{
		if(strcmp($this->state,"disconnected"))
		{
			$this->error="connection is already established";
			return(0);
		}
		$this->error=$error="";
		$this->esmtp_host="";
		$this->esmtp_extensions=array();
		$hosts=array();
		if($this->direct_delivery)
		{
			if(strlen($domain)==0)
				return(1);
			$hosts=$weights=$mxhosts=array();
			$getmxrr=$this->getmxrr;
			if(function_exists($getmxrr)
			&& $getmxrr($domain,$hosts,$weights))
			{
				for($host=0;$host<count($hosts);$host++)
					$mxhosts[$weights[$host]]=$hosts[$host];
				KSort($mxhosts);
				for(Reset($mxhosts),$host=0;$host<count($mxhosts);Next($mxhosts),$host++)
					$hosts[$host]=$mxhosts[Key($mxhosts)];
			}
			else
			{
				if(strcmp(@gethostbyname($domain),$domain)!=0)
					$hosts[]=$domain;
			}
		}
		else
		{
			if(strlen($this->host_name))
				$hosts[]=$this->host_name;
			if(strlen($this->pop3_auth_host))
			{
				$user=$this->user;
				if(strlen($user)==0)
				{
					$this->error="it was not specified the POP3 authentication user";
					return(0);
				}
				$password=$this->password;
				if(strlen($password)==0)
				{
					$this->error="it was not specified the POP3 authentication password";
					return(0);
				}
				$domain=$this->pop3_auth_host;
				$this->error=$this->connect_to_host($domain, $this->pop3_auth_port, "Resolving POP3 authentication host \"".$domain."\"...");
				if(strlen($this->error))
					return(0);
				if(strlen($response=$this->get_line())==0)
					return(0);
				if(strcmp($this->tokenize($response," "),"+OK"))
				{
					$this->error="POP3 authentication server greeting was not found";
					return(0);
				}
				if(!$this->put_line("USER ".$this->user)
				|| strlen($response=$this->get_line())==0)
					return(0);
				if(strcmp($this->tokenize($response," "),"+OK"))
				{
					$this->error="POP3 authentication user was not accepted: ".$this->tokenize("\r\n");
					return(0);
				}
				if(!$this->put_line("PASS ".$password)
				|| strlen($response=$this->get_line())==0)
					return(0);
				if(strcmp($this->tokenize($response," "),"+OK"))
				{
					$this->error="POP3 authentication password was not accepted: ".$this->tokenize("\r\n");
					return(0);
				}
				fclose($this->connection);
				$this->connection=0;
			}
		}
		if(count($hosts)==0)
		{
			$this->error="could not determine the SMTP to connect";
			return(0);
		}
		for($host=0, $error="not connected";strlen($error) && $host<count($hosts);$host++)
		{
			$domain=$hosts[$host];
			$error=$this->connect_to_host($domain, $this->host_port, "Resolving SMTP server domain \"$domain\"...");
		}
		if(strlen($error))
		{
			$this->error=$error;
			return(0);
		}
		$timeout=($this->data_timeout ? $this->data_timeout : $this->timeout);
		if($timeout
		&& function_exists("socket_set_timeout"))
			socket_set_timeout($this->connection,$timeout,0);
		if($this->debug)
			$this->output_debug("connected to SMTP server \"".$domain."\".");
		if(!strcmp($localhost=$this->localhost,"")
		&& !strcmp($localhost=getenv("SERVER_NAME"),"")
		&& !strcmp($localhost=getenv("HOST"),""))
			$localhost="localhost";
		$success=0;
		if($this->verify_result_lines("220",$responses)>0)
		{
			$fallback=1;
			if($this->esmtp
			|| strlen($this->user))
			{
				if($this->put_line("EHLO $localhost"))
				{
					if(($success_code=$this->verify_result_lines("250",$responses))>0)
					{
						$this->esmtp_host=$this->tokenize($responses[0]," ");
						for($response=1;$response<count($responses);$response++)
						{
							$extension=strtoupper($this->tokenize($responses[$response]," "));
							$this->esmtp_extensions[$extension]=$this->tokenize("");
						}
						$success=1;
						$fallback=0;
					}
					else
					{
						if($success_code==0)
						{
							$code=$this->tokenize($this->error," -");
							switch($code)
							{
								case "421":
									$fallback=0;
									break;
							}
						}
					}
				}
				else
					$fallback=0;
			}
			if($fallback)
			{
				if($this->put_line("HELO $localhost")
				&& $this->verify_result_lines("250",$responses)>0)
					$success=1;
			}
			if($success
			&& strlen($this->user)
			&& strlen($this->pop3_auth_host)==0)
			{
				if(!IsSet($this->esmtp_extensions["AUTH"]))
				{
					$this->error="server does not require authentication";
					$success=0;
				}
				else
				{
					if(strlen($this->authentication_mechanism))
						$mechanisms=array($this->authentication_mechanism);
					else
					{
						$mechanisms=array();
						for($authentication=$this->tokenize($this->esmtp_extensions["AUTH"]," ");strlen($authentication);$authentication=$this->tokenize(" "))
							$mechanisms[]=$authentication;
					}
					$credentials=array(
						"user"=>$this->user,
						"password"=>$this->password
					);
					if(strlen($this->realm))
						$credentials["realm"]=$this->realm;
					if(strlen($this->workstation))
						$credentials["workstation"]=$this->workstation;
					$success=$this->sasl_authenticate($mechanisms,$credentials,$authenticated,$mechanism);
					if(!$success
					&& !strcmp($mechanism,"PLAIN"))
					{
						/*
						 * Author:  Russell Robinson, 25 May 2003, http://www.tectite.com/
						 * Purpose: Try various AUTH PLAIN authentication methods.
						 */
						$mechanisms=array("PLAIN");
						$credentials=array(
							"user"=>$this->user,
							"password"=>$this->password
						);
						if(strlen($this->realm))
						{
							/*
							 * According to: http://www.sendmail.org/~ca/email/authrealms.html#authpwcheck_method
							 * some sendmails won't accept the realm, so try again without it
							 */
							$success=$this->sasl_authenticate($mechanisms,$credentials,$authenticated,$mechanism);
						}
						if(!$success)
						{
							/*
							 * It was seen an EXIM configuration like this:
							 * user^password^unused
							 */
							$credentials["mode"]=SASL_PLAIN_EXIM_DOCUMENTATION_MODE;
							$success=$this->sasl_authenticate($mechanisms,$credentials,$authenticated,$mechanism);
						}
						if(!$success)
						{
							/*
							 * ... though: http://exim.work.de/exim-html-3.20/doc/html/spec_36.html
							 * specifies: ^user^password
							 */
							$credentials["mode"]=SASL_PLAIN_EXIM_MODE;
							$success=$this->sasl_authenticate($mechanisms,$credentials,$authenticated,$mechanism);
						}
					}
					if($success
					&& strlen($mechanism)==0)
					{
						$this->error="it is not supported any of the authentication mechanisms required by the server";
						$success=0;
					}
				}
			}
		}
		if($success)
		{
			$this->state="connected";
			$this->connected_domain=$domain;
		}
		else
		{
			fclose($this->connection);
			$this->connection=0;
		}
		return($success);
	}

	Function mail_from($sender)
	{
		if($this->direct_delivery)
		{
			switch($this->state)
			{
				case "disconnected":
					$this->direct_sender=$sender;
					return(1);
				case "connected":
					$sender=$this->direct_sender;
					break;
				default:
					$this->error="direct delivery connection is already established and sender is already set";
					return(0);
			}
		}
		else
		{
			if(strcmp($this->state,"connected"))
			{
				$this->error="connection is not in the initial state";
				return(0);
			}
		}
		$this->error="";
		if(!$this->put_line("MAIL FROM:<$sender>"))
			return(0);
		if(!IsSet($this->esmtp_extensions["PIPELINING"])
		&& $this->verify_result_lines("250",$responses)<=0)
			return(0);
		$this->state="SenderSet";
		if(IsSet($this->esmtp_extensions["PIPELINING"]))
			$this->pending_sender=1;
		$this->pending_recipients=0;
		return(1);
	}

	Function set_recipient($recipient)
	{
		if($this->direct_delivery)
		{
			if(GetType($at=strrpos($recipient,"@"))!="integer")
				return("it was not specified a valid direct recipient");
			$domain=substr($recipient,$at+1);
			switch($this->state)
			{
				case "disconnected":
					if(!$this->connect($domain))
						return(0);
					if(!$this->mail_from(""))
					{
						$error=$this->error;
						$this->disconnect();
						$this->error=$error;
						return(0);
					}
					break;
				case "SenderSet":
				case "RecipientSet":
					if(strcmp($this->connected_domain,$domain))
					{
						$this->error="it is not possible to deliver directly to recipients of different domains";
						return(0);
					}
					break;
				default:
					$this->error="connection is already established and the recipient is already set";
					return(0);
			}
		}
		else
		{
			switch($this->state)
			{
				case "SenderSet":
				case "RecipientSet":
					break;
				default:
					$this->error="connection is not in the recipient setting state";
					return(0);
			}
		}
		$this->error="";
		if(!$this->put_line("RCPT TO:<$recipient>"))
			return(0);
		if(IsSet($this->esmtp_extensions["PIPELINING"]))
		{
			$this->pending_recipients++;
			if($this->pending_recipients>=$this->maximum_piped_recipients)
			{
				if(!$this->flush_recipients())
					return(0);
			}
		}
		else
		{
			if($this->verify_result_lines(array("250","251"),$responses)<=0)
				return(0);
		}
		$this->state="RecipientSet";
		return(1);
	}

	Function start_data()
	{
		if(strcmp($this->state,"RecipientSet"))
		{
			$this->error="connection is not in the start sending data state";
			return(0);
		}
		$this->error="";
		if(!$this->put_line("DATA"))
			return(0);
		if($this->pending_recipients)
		{
			if(!$this->flush_recipients())
				return(0);
		}
		if($this->verify_result_lines("354",$responses)<=0)
			return(0);
		$this->state="SendingData";
		return(1);
	}

	Function prepare_data(&$data,&$output,$preg=1)
	{
		if($preg
		&& function_exists("preg_replace"))
			$output=preg_replace(array("/\n\n|\r\r/","/(^|[^\r])\n/","/\r([^\n]|\$)/D","/(^|\n)\\./"),array("\r\n\r\n","\\1\r\n","\r\n\\1","\\1.."),$data);
		else
			$output=ereg_replace("(^|\n)\\.","\\1..",ereg_replace("\r([^\n]|\$)","\r\n\\1",ereg_replace("(^|[^\r])\n","\\1\r\n",ereg_replace("\n\n|\r\r","\r\n\r\n",$data))));
	}

	Function send_data($data)
	{
		if(strcmp($this->state,"SendingData"))
		{
			$this->error="connection is not in the sending data state";
			return(0);
		}
		$this->error="";
		return($this->put_data($data));
	}

	Function end_sending_data()
	{
		if(strcmp($this->state,"SendingData"))
		{
			$this->error="connection is not in the sending data state";
			return(0);
		}
		$this->error="";
		if(!$this->put_line("\r\n.")
		|| $this->verify_result_lines("250",$responses)<=0)
			return(0);
		$this->state="connected";
		return(1);
	}

	Function reset_connection()
	{
		switch($this->state)
		{
			case "connected":
				return(1);
			case "SendingData":
				$this->error="can not reset the connection while sending data";
				return(0);
			case "disconnected":
				$this->error="can not reset the connection before it is established";
				return(0);
		}
		$this->error="";
		if(!$this->put_line("RSET")
		|| $this->verify_result_lines("250",$responses)<=0)
			return(0);
		$this->state="connected";
		return(1);
	}

	Function disconnect($quit=1)
	{
		if(!strcmp($this->state,"disconnected"))
		{
			$this->error="it was not previously established a SMTP connection";
			return(0);
		}
		$this->error="";
		if(!strcmp($this->state,"connected")
		&& $quit
		&& (!$this->put_line("QUIT")
		|| $this->verify_result_lines("221",$responses)<=0))
			return(0);
		fclose($this->connection);
		$this->connection=0;
		$this->state="disconnected";
		if($this->debug)
			$this->output_debug("disconnected.");
		return(1);
	}

	Function send_message($sender,$recipients,$headers,$body)
	{
		if(($success=$this->connect()))
		{
			if(($success=$this->mail_from($sender)))
			{
				for($recipient=0;$recipient<count($recipients);$recipient++)
				{
					if(!($success=$this->set_recipient($recipients[$recipient])))
						break;
				}
				if($success
				&& ($success=$this->start_data()))
				{
					if(($success=$this->send_data($headers."\r\n")))
					{
						$this->prepare_data($body,$body_data);
						$success=$this->send_data($body_data);
					}
					if($success)
						$success=$this->end_sending_data();
				}
			}
			$error=$this->error;
			$disconnect_success=$this->disconnect($success);
			if($success)
				$success=$disconnect_success;
			else
				$this->error=$error;
		}
		return($success);
	}

};

?>