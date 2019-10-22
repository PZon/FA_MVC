<?php

namespace App;

use Mailgun\Mailgun;
use App\Config;

class Mail{
 public static function send($to, $subject, $text, $html){
	# Instantiate the client.
	$mgClient = new Mailgun(Config::MAILGUN_API_KEY);
	$domain = Config::MAILGUN_DOMAIN;
	# Make the call to the client.
	$result = $mgClient->sendMessage($domain, array(
		'from'	=> 'MVC_FA <mailgun@sandbox7245ad6fc7724ecda49aa89dcb4c50b4.mailgun.org>',
		'to'	=> $to,
		'subject' => $subject,
		'text'	=> $text,
		'html'	=> $html
	)); 	 
 }

}//endclass;