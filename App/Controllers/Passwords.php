<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

class Passwords extends \Core\Controller{
	
	public function requestResetAction(){
		User::sendPasswordReset($_POST['email']);//enebeled tmp;
		Flash::addMessage('Check your email to finish password reset process', Flash::WARNING);
	$this->redirect('/');
	}
	
	public function resetAction(){
	  $token=$this->route_params['token'];
	  
	  $user=$this->getUserOrExit($token);
	  
	  View::renderTemplate('Password/reset.html',['token'=>$token]);
	}
	
	public function resetPasswordAction(){
		$token=$_POST['token'];

		$user=$this->getUserOrExit($token);
	  
		if($user->resetPassword($_POST['pass1'])){
			View::renderTemplate('Password/reset_success.html');
		}else{
			View::renderTemplate('Password/reset.html',
							['token'=>$token,
							'user'=>$user ]);
		}
	}
	
	protected function getUserOrExit($token){
	   $user=User::findByPasswordReset($token);
	   
	   if($user){
		  return $user;
	  }else{
		  View::renderTemplate('Password/token_exp.html');
		  exit;
	  }
	}
	
	public function forgotAction(){
		View::renderTemplate('Password/forgot.html');
	}

}//end class