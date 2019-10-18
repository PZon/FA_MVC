<?php

namespace App;

class Flash{
	
	const SUCCESS ='success';
	const INFO = 'info';
	const WARNING = 'warning';
	
	public static function addMessage($message, $type='warning'){
	 if(! isset($_SESSION['flash_info'])){
	   $_SESSION['flash_info']=[];
	 }
	   $_SESSION['flash_info'][]=[
	   'body'=>$message,
	   'type'=>$type
	   ];
	}
	
	public static function getMessages(){
		if(isset($_SESSION['flash_info'])){
			$messages = $_SESSION['flash_info'];
			unset($_SESSION['flash_info']);
			return $messages;
		}
	}
}//end class