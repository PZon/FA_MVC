<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;

class User extends \Core\Model{
 public $errors=[];
 public function __construct($data=[]){
	foreach($data as $key=>$value){
		$this->$key=$value;
	};
 }
 
 public function save(){
	 
	$this->validate();
	
	if(empty($this->errors)){	
	 $pass_hash=password_hash($this->pass1, PASSWORD_DEFAULT);
	 $nick=strtoupper($this->Nick);
	 $token= new Token();
	 $hashed_token=$token->getHash();
	 $this->activation_token = $token->getValue();
	 
	 $sql='INSERT INTO users VALUES (NULL, :nick, :email, :pass_hash,NULL,NULL,:activation_hash,"N")';
	 
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 
	 $stmt->bindValue(':nick', $nick, PDO::PARAM_STR );
	 $stmt->bindValue(':email', $this->Email, PDO::PARAM_STR );
	 $stmt->bindValue(':pass_hash', $pass_hash, PDO::PARAM_STR );
	 $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR );
	 
	 return $stmt->execute();
	}
	return false;
 }
 
 public function validate(){
	 
	 if ($this->Nick == '') {
           $this->errors[] = 'Name is required';
       }

       // email address
       if (filter_var($this->Email, FILTER_VALIDATE_EMAIL) === false) {
           $this->errors[] = 'Invalid email';
       }
	   if(static::emailExist($this->Email, $this->idUser ?? null)){
		   $this->errors[]='Email already taken';
	   }

	if(isset($this->pass1)){
       if (strlen($this->pass1) < 8) {
           $this->errors[] = 'Please enter at least 8 characters for the password';
       }

       if (preg_match('/.*[a-z]+.*/i', $this->pass1) == 0) {
           $this->errors[] = 'Password needs at least one letter';
       }

       if (preg_match('/.*\d+.*/i', $this->pass1) == 0) {
           $this->errors[] = 'Password needs at least one number';
       } 
	}
 }
 
 public static function findByEmail($email){
	 $sql='SELECT * FROM users WHERE email=:email';
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->bindValue(':email', $email, PDO::PARAM_STR);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 
	 return $stmt->fetch();
 }
 
  public static function findById($idUser){
	 $sql='SELECT * FROM users WHERE idUser=:idUser';
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 
	 return $stmt->fetch();
 }
 
 public static function emailExist($email, $ignore_id=null){
	 $user=static::findByEmail($email);
	 
	 if($user){
		 if($user->idUser != $ignore_id) return true;
	 }
	 return false;
 }
 
 public static function authenticate($email, $password){
	 $user=static::findByEmail($email);
	 if($user && $user->Active=='Y'){
		if(password_verify($password, $user->Password)){
			return $user;
		}
	 }
	 return false;
 }
 
 public function rememberLogin(){
	$token = new Token();
	$hashed_token=$token->getHash();
	$this->remember_token=$token->getValue();

	$this->expiry_timestamp=time()+60*60*24*2; //2days
	
	$sql='INSERT INTO user_remembered_logins VALUES (:token_hash, :user_id, :expires_at)';
	
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 
	 $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR );
	 $stmt->bindValue(':user_id', $this->idUser, PDO::PARAM_INT );
	 $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR );
	 
	 return $stmt->execute();
 }
 
 public static function sendPasswordReset($email){
	 $user= static::findByEmail($email);
	 
	 if($user){
		 if($user->startPasswordReset()){
			$user->sendPasswordResetEmail(); 
		 }
	 } 
 }
 
 protected function startPasswordReset(){
	 $token= new Token();
	 $hashed_token=$token->getHash();
	 $this->password_reset_token=$token->getValue();
	 
	 $expiry_timestamp=time()+60*60*2; //2hours
	 
	 $sql='UPDATE users SET Password_reset_hash = :token_hash,
			Password_reset_exp = :expires_at WHERE idUser= :idUser';
			
	$db=static::getDB();
	$stmt=$db->prepare($sql);
	
	$stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR );
	$stmt->bindValue(':idUser', $this->idUser, PDO::PARAM_INT );
	$stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR );
	
	return $stmt->execute();
 }
 
 protected function sendPasswordResetEmail(){
	$url='http://'.$_SERVER['HTTP_HOST'].'/passwords/reset/'.$this->password_reset_token;
	echo $url.'- remove this echo from User obj methods!!!';//echo tmp!!!;
	$text=View::getTemplate('Password/reset_email.txt',['url'=>$url]);
	$html=View::getTemplate('Password/reset_email.html',['url'=>$url]);
	
	Mail::send($this->Email, 'Password reset', $text, $html);
	
	
 }
 
 public static function findByPasswordReset($token){
	 $token= new Token($token);
	 $hashed_token=$token->getHash();
	 
	 $sql='SELECT * FROM users WHERE Password_reset_hash = :token_hash';
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->bindParam(':token_hash', $hashed_token, PDO::PARAM_STR);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 
	 $user=$stmt->fetch();
	 
	 if($user){
		 if(strtotime($user->Password_reset_exp)>time()){
			 return $user;
		 }
	 }
	 
 }
 
 public function resetPassword($password){
	 $this->pass1=$password;
	 $this->validate();
	 
	if(empty($this->errors)){
	   $password_hash=password_hash($this->pass1, PASSWORD_DEFAULT);
	   
	   $sql='UPDATE users SET Password = :password_hash,
			Password_reset_hash=NULL, Password_reset_exp=NULL
			WHERE idUser= :idUser';
			
	$db=static::getDB();
	$stmt=$db->prepare($sql);
	
	$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR );
	$stmt->bindValue(':idUser', $this->idUser, PDO::PARAM_INT );
	
	return $stmt->execute();
	}
	return false;
 }
 
  public function sendActivationEmail(){
	$url='http://'.$_SERVER['HTTP_HOST'].'/register/activate/'.$this->activation_token;
	echo $url.'- remove this echo from User obj methods!!!';//echo tmp!!!;
	$text=View::getTemplate('Signup/activation_email.txt',['url'=>$url]);
	$html=View::getTemplate('Signup/activation_email.html',['url'=>$url]);
	
	Mail::send($this->email, 'Account activation', $text, $html);
	
 }
 
 public static function activate($value){
	 $token= new Token($value);
	 $hashed_token=$token->getHash();	
	
	$sql='UPDATE users SET activation_hash = null, active = "Y" WHERE activation_hash = :hashed_token';
	
	$db=static::getDB();
	$stmt=$db->prepare($sql);
	
	$stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR );
	$stmt->execute();
 } 
 
 public function updateProfile($data){
	 $this->name=$data['name'];
	 $this->email=$data['email'];
	 
	if($data['password']!=''){
	 $this->password=$data['password'];
	}

	 
   if(empty($this->errors)){
	  $sql= 'UPDATE users SET name= :name, email=:email';
	 
	 if(isset($this->password)){
	  $sql.=', password_hash=:password_hash';
	 }
	  $sql.=' WHERE id=:id';
	  
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
			
	 $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
	 $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
	 $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
	 
	 if(isset($this->password)){
	   $password_hash=password_hash($this->password, PASSWORD_DEFAULT);
	   $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
	 }
	 return $stmt->execute();
   }
   return false;
 }
 
 
}//end class










