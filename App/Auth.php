<?php

namespace App;

use App\Models\User;
use App\Models\RememberedLogin;

class Auth{
	
	public static function login($user, $remember_me){
		session_regenerate_id(true);
		$_SESSION['idUser']=$user->idUser;
		
		if($remember_me) {
			if($user->rememberLogin()){
			 setcookie('remember_me', $user->remember_token, $user->expiry_timestamp, '/');
			}
		}
	}
	
	public static function logout(){
	  // Unset all of the session variables.
	 $_SESSION = array();

	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	 if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	 }

	 // Finally, destroy the session.
	 session_destroy();
	 static::forgetLogin();
	}
	
	public static function rememberRequestedPage(){
	 $_SESSION['returnTo']=$_SERVER['REQUEST_URI'];
	}
	
	public static function getReturnToPage(){
	// zmienic na widok statementu
	  return $_SESSION['returnTo'] ?? '/Login/VerifiedUser?view=cm';
	}
	
	public static function getUser(){
		if(isset($_SESSION['idUser'])){
			return User::findById($_SESSION['idUser']);
		}else{
			return static::loginFromCookie();
		}
	}
	
	protected static function loginFromCookie(){
		$cookie=$_COOKIE['remember_me'] ?? false;
		
		if($cookie){
		 $remembered_login=RememberedLogin::findByToken($cookie);
		 if($remembered_login && ! $remembered_login->hasExpired()){
			 $user=$remembered_login->getUser();
			 static::login($user, false);
			 return $user;
		 }
		}
	}
	
	protected static function forgetLogin(){
		$cookie=$_COOKIE['remember_me'] ?? false;
		
		if($cookie){
		 $remembered_login=RememberedLogin::findByToken($cookie);
		 if($remembered_login && ! $remembered_login->hasExpired()){
			 if($remembered_login){
				 $remembered_login->deleteLoginCookie();
			 }
			setcookie('remember_me','',time()-3600);//date in past
		 }
		}
	}
	

	
}//end class;