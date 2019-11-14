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
	$this->currentYM=Period::setCurrentYM();
	$this->prevYM=Period::setPreviousYM();
	$this->prevYMEnd=Period::setPreviousYMEnd();
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
    $stmt->bindValue(':transactionDate', $transactionDate, PDO::PARAM_STR);
    $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
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
 
 public function getIncomesCM(){
	 $sql="SELECT i.idIncome, i.idIncomeCat, i.incomeDate, i.incomeAmount, i.incomeDescr, c.nameCatI FROM income i 
	 JOIN in_cat c ON (c.idCatI=i.idIncomeCat) 
	 WHERE i.idUser={$_SESSION['idUser']} AND i.incomeDate >= '$this->currentYM'
	 UNION
	 SELECT i.idIncome, i.idIncomeCat, i.incomeDate, i.incomeAmount, i.incomeDescr, u.nameUserCatIn FROM income i 
	 JOIN user_in_cat u ON (u.idUserCatIn=i.idIncomeCat)
	 WHERE i.idUser={$_SESSION['idUser']} AND i.incomeDate >= '$this->currentYM'
	 ORDER BY incomeDate";
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $result;
 }
 
 function getExpensesCM(){
	 $sql="SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, c.nameCatE, p.nameCatPay FROM expenses e 
	 JOIN ex_cat c ON (c.idCatE = e.idExpensesCat) 
	 JOIN pay_cat p ON (p.idCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND e.expenseDate >= '$this->currentYM'
	 UNION 
	 SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, u.nameUserCatEx, a.nameUserCatPay FROM expenses e 
	 JOIN user_ex_cat u ON (u.idUserCatEx = e.idExpensesCat) 
	 JOIN user_pay_cat a ON (a.idUserCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND e.expenseDate >= '$this->currentYM'
	 ORDER BY expenseDate";
	 
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $result;
	}
 
 public function getIncomesPM(){
	 $sql="SELECT i.idIncome, i.idIncomeCat, i.incomeDate, i.incomeAmount, i.incomeDescr, c.nameCatI FROM income i 
	 JOIN in_cat c ON (c.idCatI=i.idIncomeCat)
	 WHERE i.idUser={$_SESSION['idUser']} 
	 AND i.incomeDate BETWEEN '$this->prevYM' AND '$this->prevYMEnd' 
	 UNION
	 SELECT i.idIncome, i.idIncomeCat, i.incomeDate, i.incomeAmount, i.incomeDescr, u.nameUserCatIn FROM income i 
	 JOIN user_in_cat u ON (u.idUserCatIn=i.idIncomeCat)
	 WHERE i.idUser={$_SESSION['idUser']} 
	 AND i.incomeDate BETWEEN '$this->prevYM' AND '$this->prevYMEnd'ORDER BY incomeDate";
	 
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $result; 
 }
 
 function getExpensesPM(){
	 $sql="SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, c.nameCatE, p.nameCatPay FROM expenses e 
	 JOIN ex_cat c ON (c.idCatE = e.idExpensesCat) 
	 JOIN pay_cat p ON (p.idCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND
	 e.expenseDate BETWEEN '$this->prevYM' AND '$this->prevYMEnd'
	 UNION 
	 SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, u.nameUserCatEx, a.nameUserCatPay FROM expenses e 
	 JOIN user_ex_cat u ON (u.idUserCatEx = e.idExpensesCat) 
	 JOIN user_pay_cat a ON (a.idUserCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND 
	 e.expenseDate BETWEEN '$this->prevYM' AND '$this->prevYMEnd'ORDER BY expenseDate";
	 
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $result; 
	}
	
  function getIncomesCP($dateFrom, $dateTo){
	 $sql="SELECT i.idIncome, i.idIncomeCat, i.incomeDate, i.incomeAmount, i.incomeDescr, c.nameCatI FROM income i 
	 JOIN in_cat c ON (c.idCatI=i.idIncomeCat)
	 WHERE i.idUser={$_SESSION['idUser']} 
	 AND i.incomeDate BETWEEN '$dateFrom' AND '$dateTo' 
	 UNION
	 SELECT i.idIncome, i.idIncomeCat, i.incomeDate, i.incomeAmount, i.incomeDescr, u.nameUserCatIn FROM income i 
	 JOIN user_in_cat u ON (u.idUserCatIn=i.idIncomeCat)
	 WHERE i.idUser={$_SESSION['idUser']} 
	 AND i.incomeDate BETWEEN '$dateFrom' AND '$dateTo' ORDER BY incomeDate";
	 
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $result;
 }
 
  function getExpensesCP( $dateFrom, $dateTo){
	 $sql="SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, c.nameCatE, p.nameCatPay FROM expenses e 
	 JOIN ex_cat c ON (c.idCatE = e.idExpensesCat) 
	 JOIN pay_cat p ON (p.idCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND
	 e.expenseDate BETWEEN '$dateFrom' AND '$dateTo'
	 UNION 
	 SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, u.nameUserCatEx, a.nameUserCatPay FROM expenses e 
	 JOIN user_ex_cat u ON (u.idUserCatEx = e.idExpensesCat) 
	 JOIN user_pay_cat a ON (a.idUserCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND 
	 e.expenseDate BETWEEN '$dateFrom' AND '$dateTo' ORDER BY expenseDate";

	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $result;
	}
	
 public static function getSingleTransaction($transId){
	  $sql="SELECT i.idIncome, i.idIncomeCat, i.incomeDate, i.incomeAmount, i.incomeDescr, c.nameCatI FROM income i 
	 JOIN in_cat c ON (c.idCatI=i.idIncomeCat) 
	 WHERE i.idUser={$_SESSION['idUser']} AND i.idIncome = :transId
	 UNION
	 SELECT i.idIncome, i.idIncomeCat, i.incomeDate, i.incomeAmount, i.incomeDescr, u.nameUserCatIn FROM income i 
	 JOIN user_in_cat u ON (u.idUserCatIn=i.idIncomeCat)
	 WHERE i.idUser={$_SESSION['idUser']} AND i.idIncome = :transId";
	 
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->bindParam(':transId', $transId, PDO::PARAM_INT);
	 $stmt->execute();
     return $stmt->fetch();
 }
 
  public static function deleteTransaction($transId){
	  $sql="DELETE FROM income WHERE idIncome = :transId and idUser={$_SESSION['idUser']}";
	 
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->bindParam(':transId', $transId, PDO::PARAM_INT);
	 $stmt->execute();
	 return true;
 }
 
 public static function updateTransaction($transId){
	 $transactionDate=filter_input(INPUT_POST,'incomeDate',FILTER_SANITIZE_STRING);
	 $amount=filter_input(INPUT_POST,'incomeAmount',FILTER_SANITIZE_STRING);
	 $description=filter_input(INPUT_POST,'incomeDescription',FILTER_SANITIZE_STRING);
	 if(isset($_POST['transType'])){
	  $sql="UPDATE income SET idIncomeCat = :idIncomeCat, incomeDate = :incomeDate, incomeAmount = :incomeAmount, incomeDescr = :incomeDescr WHERE idIncome = :idIncome and idUser={$_SESSION['idUser']}";
	 
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->bindParam(':idIncome', $transId, PDO::PARAM_INT);
	 $stmt->bindValue(':idIncomeCat', $_POST['incomeCategory'], PDO::PARAM_INT);
     $stmt->bindValue(':incomeDate', $transactionDate, PDO::PARAM_STR);
     $stmt->bindValue(':incomeAmount', $amount, PDO::PARAM_STR);
     $stmt->bindValue(':incomeDescr', $description, PDO::PARAM_STR);
		return $stmt->execute();
	 }else return false;
 }
 
}//end class