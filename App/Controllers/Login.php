<?php
namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

class Login extends \Core\Controller{
	
 public function createAction(){
	$user=User::authenticate($_POST['email'], $_POST['pass']);
	
	$remember_me = isset($_POST['remember_me']);
	
	if($user){
		Auth::login($user, $remember_me);
		Flash::addMessage('Login successful');
		$this->redirect(Auth::getReturnToPage());
	}else {
	 View::renderTemplate('Home/home.html',
		['email'=>$_POST['email'],
		 'remember_me'=>$remember_me
		]);
	
	Flash::addMessage('Login unsuccessful. Try again', Flash::WARNING);
	}
 }
 
 public function destroyAction(){
	Auth::logout();
	$this->redirect('/login/show-logout-info');
 }
 
 public function showLogoutInfo(){
		
	Flash::addMessage('Logout successful');
	$this->redirect('/'); 
 }

 
}//end class;
?>