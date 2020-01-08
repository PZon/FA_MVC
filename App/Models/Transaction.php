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
  $sql="SELECT idCatE, nameCatE, Expense_Limit FROM ex_cat 
   UNION 
   SELECT idUserCatEx, nameUserCatEx, UExLimit FROM user_ex_cat 
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
	 $amount=filter_input(INPUT_POST,'amount',FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
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
 
 public function getExpensesCM(){
	 $sql="SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, c.nameCatE, p.nameCatPay FROM expenses e 
	 JOIN ex_cat c ON (c.idCatE = e.idExpensesCat) 
	 JOIN pay_cat p ON (p.idCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND e.expenseDate >= '$this->currentYM'
	 UNION 
	 SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, u.nameUserCatEx, a.nameUserCatPay FROM expenses e 
	 JOIN user_ex_cat u ON (u.idUserCatEx = e.idExpensesCat) 
	 JOIN user_pay_cat a ON (a.idUserCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND e.expenseDate >= '$this->currentYM' 
	 UNION 
	 SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, u.nameUserCatEx, p.nameCatPay FROM expenses e 
	 JOIN user_ex_cat u ON (u.idUserCatEx = e.idExpensesCat)  
	 JOIN pay_cat p ON (p.idCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND e.expenseDate >= '$this->currentYM' 
	 UNION 
	 SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, c.nameCatE, a.nameUserCatPay FROM expenses e 
	 JOIN ex_cat c ON (c.idCatE = e.idExpensesCat) 
	 JOIN user_pay_cat a ON (a.idUserCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND e.expenseDate >= '$this->currentYM' ORDER BY expenseDate";
	 
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
	 e.expenseDate BETWEEN '$this->prevYM' AND '$this->prevYMEnd'
	 UNION 
	 SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, u.nameUserCatEx, p.nameCatPay FROM expenses e 
	 JOIN user_ex_cat u ON (u.idUserCatEx = e.idExpensesCat)  
	 JOIN pay_cat p ON (p.idCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND
	 e.expenseDate BETWEEN '$this->prevYM' AND '$this->prevYMEnd' 
	 UNION 
	 SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, c.nameCatE, a.nameUserCatPay FROM expenses e 
	 JOIN ex_cat c ON (c.idCatE = e.idExpensesCat) 
	 JOIN user_pay_cat a ON (a.idUserCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND
	 e.expenseDate BETWEEN '$this->prevYM' AND '$this->prevYMEnd' ORDER BY expenseDate";
	 
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
	 e.expenseDate BETWEEN '$dateFrom' AND '$dateTo'
	 UNION 
	 SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, u.nameUserCatEx, p.nameCatPay FROM expenses e 
	 JOIN user_ex_cat u ON (u.idUserCatEx = e.idExpensesCat)  
	 JOIN pay_cat p ON (p.idCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND
	 e.expenseDate BETWEEN '$dateFrom' AND '$dateTo' 
	 UNION 
	 SELECT e.idExpenses, e.expenseDate, e.expenseAmount, e.expenseDescr, c.nameCatE, a.nameUserCatPay FROM expenses e 
	 JOIN ex_cat c ON (c.idCatE = e.idExpensesCat) 
	 JOIN user_pay_cat a ON (a.idUserCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND
	 e.expenseDate BETWEEN '$dateFrom' AND '$dateTo' ORDER BY expenseDate";

	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $result;
	}
	
 public static function getSingleTransaction(){
	if(isset($_SESSION['incomeId'])){
	 $sql="SELECT i.idIncome, i.idIncomeCat, i.incomeDate, i.incomeAmount, i.incomeDescr, c.nameCatI FROM income i 
	 JOIN in_cat c ON (c.idCatI=i.idIncomeCat) 
	 WHERE i.idUser={$_SESSION['idUser']} AND i.idIncome = {$_SESSION['incomeId']}
	 UNION
	 SELECT i.idIncome, i.idIncomeCat, i.incomeDate, i.incomeAmount, i.incomeDescr, u.nameUserCatIn FROM income i 
	 JOIN user_in_cat u ON (u.idUserCatIn=i.idIncomeCat)
	 WHERE i.idUser={$_SESSION['idUser']} AND i.idIncome = {$_SESSION['incomeId']}";
	}else if(isset($_SESSION['expenseId'])){
	 $sql="SELECT e.idExpenses, e.idExpensesCat, e.userPayMethId, e.expenseDate, e.expenseAmount, e.expenseDescr, c.nameCatE, p.nameCatPay FROM expenses e 
	 JOIN ex_cat c ON (c.idCatE = e.idExpensesCat) 
	 JOIN pay_cat p ON (p.idCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND e.idExpenses = {$_SESSION['expenseId']}
	 UNION 
	 SELECT e.idExpenses, e.idExpensesCat, e.userPayMethId, e.expenseDate, e.expenseAmount, e.expenseDescr, u.nameUserCatEx, a.nameUserCatPay FROM expenses e 
	 JOIN user_ex_cat u ON (u.idUserCatEx = e.idExpensesCat) 
	 JOIN user_pay_cat a ON (a.idUserCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND e.idExpenses = {$_SESSION['expenseId']}
	 UNION 
	 SELECT e.idExpenses, e.idExpensesCat, e.userPayMethId, e.expenseDate, e.expenseAmount, e.expenseDescr, u.nameUserCatEx, p.nameCatPay FROM expenses e 
	 JOIN user_ex_cat u ON (u.idUserCatEx = e.idExpensesCat)  
	 JOIN pay_cat p ON (p.idCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND e.idExpenses = {$_SESSION['expenseId']} 
	 UNION 
	 SELECT e.idExpenses, e.idExpensesCat, e.userPayMethId, e.expenseDate, e.expenseAmount, e.expenseDescr, c.nameCatE, a.nameUserCatPay FROM expenses e 
	 JOIN ex_cat c ON (c.idCatE = e.idExpensesCat) 
	 JOIN user_pay_cat a ON (a.idUserCatPay = e.userPayMethId) WHERE e.idUser={$_SESSION['idUser']} AND e.idExpenses = {$_SESSION['expenseId']} ";
	}
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->execute();
     return $stmt->fetch();
 }
 
  public static function deleteTransaction(){
	 if(isset($_SESSION['incomeId'])){
	  $sql="DELETE FROM income WHERE idIncome = {$_SESSION['incomeId']} and idUser={$_SESSION['idUser']}";
	 }else if(isset($_SESSION['expenseId'])){
	  $sql="DELETE FROM expenses WHERE idExpenses = {$_SESSION['expenseId']} and idUser={$_SESSION['idUser']}";
	 }
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->execute();
	 return true;
 }
 
 public static function updateTransaction(){
	 $transactionDate=filter_input(INPUT_POST,'transactionDate',FILTER_SANITIZE_STRING);
	 $amount=filter_input(INPUT_POST,'transactionAmount',FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
	 $description=filter_input(INPUT_POST,'transactionDescription',FILTER_SANITIZE_STRING);
	 
	 if(isset($_SESSION['incomeId'])){
	  $sql="UPDATE income SET idIncomeCat = :idIncomeCat, incomeDate = :transactionDate, incomeAmount = :amount, incomeDescr = :description WHERE idIncome = {$_SESSION['incomeId']} and idUser={$_SESSION['idUser']}";
	 }else if(isset($_SESSION['expenseId'])){
	  $sql="UPDATE expenses SET idExpensesCat = :idExpensesCat, userPayMethId = :userPayMethId, expenseDate = :transactionDate, expenseAmount = :amount, expenseDescr = :description WHERE idExpenses = {$_SESSION['expenseId']} and idUser={$_SESSION['idUser']}"; 
	 }
	 
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 if(isset($_SESSION['incomeId'])){
	  $stmt->bindValue(':idIncomeCat', $_POST['incomeCategory'], PDO::PARAM_INT);
	 }else if(isset($_SESSION['expenseId'])){
	  $stmt->bindValue(':idExpensesCat', $_POST['expenseCategory'], PDO::PARAM_INT);
	  $stmt->bindValue(':userPayMethId', $_POST['payCategory'], PDO::PARAM_INT); 
	 }
      $stmt->bindValue(':transactionDate', $transactionDate, PDO::PARAM_STR);
      $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
      $stmt->bindValue(':description', $description, PDO::PARAM_STR);
		return $stmt->execute();
 }
 
 public static function totalIncomes(){
	 $sql="SELECT SUM(incomeAmount) AS sumI FROM income WHERE idUser={$_SESSION['idUser']}";
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->execute();
     return $stmt->fetch();
 }
 
  public static function totalExpenses(){
	 $sql="SELECT SUM(expenseAmount) AS sumE FROM expenses WHERE idUser={$_SESSION['idUser']}";
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->execute();
     return $stmt->fetch();
 }
 
 public static function groupedIncomes(){
	 $sql="SELECT i.idIncomeCat, SUM(i.incomeAmount) AS totalI, c.nameCatI FROM income i 
	 JOIN in_cat c ON (c.idCatI=i.idIncomeCat) 
	 WHERE i.idUser={$_SESSION['idUser']}
     GROUP BY i.idIncomeCat
	 UNION
	 SELECT i.idIncomeCat, SUM(i.incomeAmount) AS totalI , u.nameUserCatIn FROM income i 
	 JOIN user_in_cat u ON (u.idUserCatIn=i.idIncomeCat)
	 WHERE i.idUser={$_SESSION['idUser']}
     GROUP BY i.idIncomeCat";
	 
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $result;
 }
 
  public static function groupedExpenses(){
	 $sql="SELECT e.idExpensesCat, SUM(e.expenseAmount) AS totalE, c.nameCatE  FROM expenses e 
	 JOIN ex_cat c ON (c.idCatE = e.idExpensesCat) 
 	 WHERE e.idUser={$_SESSION['idUser']}
     GROUP BY e.idExpensesCat
	 UNION 
	 SELECT e.idExpensesCat, SUM(e.expenseAmount) AS totalE,  u.nameUserCatEx FROM expenses e 
	 JOIN user_ex_cat u ON (u.idUserCatEx = e.idExpensesCat) 
	 WHERE e.idUser={$_SESSION['idUser']}
     GROUP BY e.idExpensesCat";
	 
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $result;
 }
 
   public static function groupedExpensesCM($idCat){
	$period=Period::setCurrentYM();
	
	$sql="SELECT idUser, idExpensesCat, SUM(expenseAmount) AS totalE FROM expenses WHERE idUser={$_SESSION['idUser']} AND idExpensesCat=$idCat AND expenseDate >='$period'";
	 
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->execute();
     return $stmt->fetch();
 }
 
}//end class