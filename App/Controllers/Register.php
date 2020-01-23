<?php
namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\User;
use \App\Models\UserCategory;

class Register extends \Core\Controller{

 public function registerAction(){					
	View::renderTemplate('Register/register.html');
 }
 
 public function createAction(){
	$email=$_POST['Email'];
	$user=new User($_POST);
   if($user->save()){
    $userID=$user->findByEmail($email);
	UserCategory::createUserCats($userID->idUser);
	//$user->sendActivationEmail(); 
	$this->redirect('/register/success');
	exit();
   }else{
	 View::renderTemplate('Register/register.html',['user'=>$user]);  
   }
 }
 
  public function successAction(){
	View::renderTemplate('Register/success.html');
 }
 
  public function activateAction(){
	 User::activate($this->route_params['token']);
	 $this->redirect('/register/activated');
 }
 
 public function activatedAction(){
	Flash::addMessage('Your account has been actvivated.', Flash::WARNING);
	$this->redirect('/');
 }
 
 
}//end class;
?>