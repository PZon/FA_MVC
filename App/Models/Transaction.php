<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Period;

class Transaction extends \Core\Model{

 public $errors=[];
 public function __construct($data=[]){
	foreach($data as $key=>$value){
		$this->$key=$value;
	};
 }
 
 public static function getIncomeCat(){
	$sql="SELECT idCatI, nameCatI FROM in_cat 
	 UNION 
	 SELECT idUserCatIn, nameUserCatIn FROM user_in_cat 
	 WHERE idUser={$_SESSION['idUser']}";
	$db=static::getDB();
	$stmt=$db->prepare($sql);
	$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	$stmt->execute();
	$result=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
 }
 
 public static function getExpenseCat(){
  $sql="SELECT idCatE, nameCatE FROM ex_cat 
   UNION 
   SELECT idUserCatEx, nameUserCatEx FROM user_ex_cat 
   WHERE idUser={$_SESSION['idUser']}";
	$db=static::getDB();
	$stmt=$db->prepare($sql);
	$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	$stmt->execute();
	$result=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

 public static function getPayCat(){
 $sql="SELECT idCatPay, nameCatPay FROM pay_cat
 UNION 
 SELECT idUserCatPay, nameUserCatPay FROM user_pay_cat 
 WHERE idUser={$_SESSION['idUser']}";
	$db=static::getDB();
	$stmt=$db->prepare($sql);
	$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	$stmt->execute();
	$result=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}
 
 public function saveTransaction(){
	 
  $this->validateTransaction();
  
  if(empty($this->errors)){
   	 $transactionDate=filter_input(INPUT_POST,'transactionDate',FILTER_SANITIZE_STRING);
	 $amount=filter_input(INPUT_POST,'amount',FILTER_SANITIZE_STRING);
	 $description=filter_input(INPUT_POST,'description',FILTER_SANITIZE_STRING);
	 $userId=$_SESSION['idUser'];
	
	 if($this->transactionType=='I'){	
	  $query='INSERT INTO income VALUES (NULL, :userId, :category, :transactionDate, :amount, :description )';
	 }else if($this->transactionType=='E'){
	  $query='INSERT INTO expenses VALUES (NULL, :userId, :category, :payType, :transactionDate, :amount, :description )';
	 }
	 
	$db=static::getDB();
	$stmt=$db->prepare($query);	  
	
	 
    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':category', $this->Category, PDO::PARAM_INT);
    $stmt->bindValue(':transactionDate', 			  $transactionDate, PDO::PARAM_STR);
    $stmt->bindValue(':amount', $amount, PDO::        PARAM_STR);
    $stmt->bindValue(':description', $description,        PDO::PARAM_STR);
	if(isset($this->payType)){
	 $stmt->bindValue(':payType', $this->payType, PDO::PARAM_INT);
	}
    return $stmt->execute();
   }
   return false;
 }//end method;	
 
 private function validateTransaction(){

	if($this->transactionDate=='' || $this->amount=='' || $this->Category==0 || $this->description=''){
		$this->errors[] = 'All fields are required';
	}else if ($this->transactionType=='E'&& $this->   payType==0){
		$this->errors[] = 'All fields are required';
	}
	
	if(is_numeric($this->amount)==false){
		$this->errors[] = 'Incorrect amount, Use (dot) . (not comma ,) to write decimals' ;
	}
	
	if(!is_string($this->description)){
		$this->errors[] = 'In description you can use just alphanumeric characters';
	}
 }
 
}//end class