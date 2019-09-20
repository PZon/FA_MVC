<?php
namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\User;//tmp

class Register extends \Core\Controller{

 public function registerAction(){					
	View::renderTemplate('Register/register.html');
 }
 
 public function createAction(){
	$user=new User($_POST);
   if($user->save()){
	//tmp $user->sendActivationEmail(); 
	$this->redirect('/register/success');
	exit();
   }else{
	 View::renderTemplate('Register/register.html',['user'=>$user]);  
   }
 }
 
  public function successAction(){
	View::renderTemplate('register/success.html');
 }
 
 
}//end class;
?>