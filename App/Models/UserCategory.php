<?php
namespace App\Models;

use PDO;
use \Core\View;
use \App\Period;

class UserCategory extends \Core\Model{

 public $errors=[];
 public function __construct($data=[]){
	foreach($data as $key=>$value){
		$this->$key=$value;
	};
 }
	public function saveCategory(){
	
	$this->validate();
	
	if(empty($this->errors)){
	$category=filter_input(INPUT_POST,'category',FILTER_SANITIZE_STRING);
	$this->expLimit=filter_input(INPUT_POST,'expLimit',FILTER_SANITIZE_NUMBER_INT);
	$category=strtoupper($this->category);
	 if($this->categoryType=='UI'){
		$sql="INSERT INTO user_in_cat VALUES (NULL, {$_SESSION['idUser']}, :category)";
	 }else if($this->categoryType=='UE'){
		$sql="INSERT INTO user_ex_cat VALUES (NULL, {$_SESSION['idUser']}, :category, :limit)";
	 }else if($this->categoryType=='UP'){
		$sql="INSERT INTO user_pay_cat VALUES (NULL, {$_SESSION['idUser']}, :category)";
	 }
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 
	 $stmt->bindValue(':category', $category, PDO::PARAM_STR );
	 if($this->categoryType=='UE'){
	  $stmt->bindValue(':limit', $this->expLimit, PDO::PARAM_INT );
	 }
	 return $stmt->execute();
	}
	return false;
 }
 
 public function validate(){

	if ($this->category == '') {
           $this->errors[] = 'Category name is required';
       }
	/*if(empty($category)){
		   echo '<span class="formError">Category name is required!</span>'; 
			$errorEmpty=true;
	   }*/
	   
	if(static::categoryExist($this->category, $this->categoryType)){
		   $this->errors[]='Category name already exist';
	   }
	 if($this->categoryType=='UE'){
	  if ($this->expLimit < 0 || $this->expLimit > 100000) {
           $this->errors[] = 'Value for expenses category should be set between 0 and 100000';
       }
	   if (!empty($this->expLimit)&&!is_numeric($this->expLimit)){
           $this->errors[] = 'wrong field value';
       }
	 }
 }
 
 public static function findByCatName($catName, $type){
	 $cat=strtoupper($catName);
	/* $sqlE_old="SELECT idCatE, nameCatE FROM ex_cat WHERE nameCatE = :catName
	 UNION 
	 SELECT idUserCatEx, nameUserCatEx FROM user_ex_cat 
	 WHERE idUser={$_SESSION['idUser']} AND nameUserCatEx = :catName ";*/
	 $sqlE="SELECT idUserCatEx, nameUserCatEx FROM user_ex_cat WHERE idUser={$_SESSION['idUser']} AND nameUserCatEx = :catName ";
	/* $sqlI_old="SELECT idCatI, nameCatI FROM in_cat WHERE nameCatI=:catName 
	 UNION 
	 SELECT idUserCatIn, nameUserCatIn FROM user_in_cat WHERE idUser={$_SESSION['idUser']} AND nameUserCatIn=:catName ";*/
	 $sqlI="SELECT idUserCatIn, nameUserCatIn FROM user_in_cat WHERE idUser={$_SESSION['idUser']} AND nameUserCatIn=:catName ";
	 /*$sqlP_old="SELECT idCatPay, nameCatPay FROM pay_cat WHERE nameCatPay=:catName
	 UNION 
	 SELECT idUserCatPay, nameUserCatPay FROM user_pay_cat 
	 WHERE idUser= {$_SESSION['idUser']} AND nameUserCatPay=:catName";*/
	 $sqlP="SELECT idUserCatPay, nameUserCatPay FROM user_pay_cat WHERE idUser= {$_SESSION['idUser']} AND nameUserCatPay=:catName";
	 $db=static::getDB();
	 
	 if($type=='UI'||$type=="I"){
		$stmt=$db->prepare($sqlI);
	 }else if($type=='UE'||$type=="E"){
		$stmt=$db->prepare($sqlE);
	 }else if($type=='UP'||$type=="P"){
		$stmt=$db->prepare($sqlP);
	 }
	 
	 $stmt->bindValue(':catName', $cat, PDO::PARAM_STR);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 
	 return $stmt->fetch();
 }
 
  public static function categoryExist($catName, $type){
	 $category=static::findByCatName($catName, $type);
	 
	 if($category){
		return true;
	 }
	 return false;
 }
 
 public static function userIncomeCat(){
	$sql="SELECT * FROM user_in_cat WHERE idUser={$_SESSION['idUser']} ORDER BY nameUserCatIn ASC"; 
	$db=static::getDB();
	$stmt=$db->prepare($sql);
	$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	$stmt->execute();
	$result=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
 }
 
  public static function userExpenseCat(){
	$sql="SELECT * FROM user_ex_cat WHERE idUser={$_SESSION['idUser']} ORDER BY nameUserCatEx ASC"; 
	$db=static::getDB();
	$stmt=$db->prepare($sql);
	$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	$stmt->execute();
	$result=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
 }
 
  public static function userPaymentCat(){
	$sql="SELECT * FROM user_pay_cat WHERE idUser={$_SESSION['idUser']} ORDER BY nameUserCatPay ASC"; 
	$db=static::getDB();
	$stmt=$db->prepare($sql);
	$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	$stmt->execute();
	$result=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
 }
 
   public static function getUserSingleCat($catType,$idCat){
	if($catType=='E'){
	 $sql="SELECT * FROM user_ex_cat WHERE idUser= {$_SESSION['idUser']} and idUserCatEx=$idCat";
	}else if($catType=='I'){
	 $sql="SELECT * FROM user_in_cat WHERE idUser= {$_SESSION['idUser']} and idUserCatIn=$idCat";
	}else if($catType=='P'){
	 $sql="SELECT * FROM user_pay_cat WHERE idUser= {$_SESSION['idUser']} and idUserCatPay=$idCat";
	}
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->execute();
     return $stmt->fetch();
 }
 
 public static function deleteUserSingleCat($catType,$idCat){
	
	if($catType=='E'){
	 $sql="DELETE FROM user_ex_cat WHERE idUser= {$_SESSION['idUser']} and idUserCatEx=$idCat";
	}else if($catType=='I'){
	 $sql="DELETE FROM user_in_cat WHERE idUser= {$_SESSION['idUser']} and idUserCatIn=$idCat";
	}else if($catType=='P'){
	 $sql="DELETE FROM user_pay_cat WHERE idUser= {$_SESSION['idUser']} and idUserCatPay=$idCat";
	}
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->execute();
     return true;
 }
 
 public static function findTransactionByCatId($catType,$idCat){
	if($catType=='E'){
	 $sql="SELECT * FROM expenses WHERE idUser= {$_SESSION['idUser']} and idExpensesCat=$idCat";
	}else if($catType=='I'){
	 $sql="SELECT * FROM income WHERE idUser= {$_SESSION['idUser']} and idIncomeCat=$idCat";
	}else if($catType=='P'){
	 $sql="SELECT * FROM expenses WHERE idUser= {$_SESSION['idUser']} and userPayMethId=$idCat";
	}
	
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 return $stmt->fetchAll(PDO::FETCH_ASSOC);
     
	 
 }
 
  public static function categoryUsed($catType,$idCat){
	$catUsed=static::findTransactionByCatId($catType,$idCat);
	 if($catUsed){
		return true;
	 }
	 return false;
 }
 
 public function updateUserCategory(){
	 $categoryType=filter_input(INPUT_POST,'categoryType',FILTER_SANITIZE_STRING);
	 $idCat=filter_input(INPUT_POST,'idCat',FILTER_SANITIZE_NUMBER_INT);
	 $category=filter_input(INPUT_POST,'category',FILTER_SANITIZE_STRING);
	 if($categoryType=='E'){
		$expLimit=filter_input(INPUT_POST,'expLimit',FILTER_SANITIZE_NUMBER_INT);
	}
	$category=strtoupper($category);
	 
	 if($categoryType=='I'){
	  $sql="UPDATE user_in_cat SET nameUserCatIn = :category WHERE idUserCatIn = :idCat and idUser={$_SESSION['idUser']}";
	 }else if($categoryType=='E'){
	  $sql="UPDATE user_ex_cat SET nameUserCatEx = :category, UExLimit=:expLimit WHERE idUserCatEx = :idCat and idUser={$_SESSION['idUser']}"; 
	 }else if($categoryType=='P'){
	  $sql="UPDATE user_pay_cat SET nameUserCatPay = :category WHERE idUserCatPay = :idCat and idUser={$_SESSION['idUser']}"; 
	 }
	 
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 if($categoryType=='E'){
	  $stmt->bindValue('expLimit', $expLimit, PDO::PARAM_INT);
	 }
      $stmt->bindValue(':category', $category, PDO::PARAM_STR);
      $stmt->bindValue('idCat', $idCat, PDO::PARAM_INT);
		return $stmt->execute();
 }
 
 public static function createUserCats($idUser){
	 $sql="
	 INSERT INTO user_pay_cat(nameUserCatPay)SELECT nameCatPay FROM pay_cat;
	 UPDATE user_pay_cat SET idUser = $idUser WHERE idUser is null;

	 INSERT INTO user_in_cat(nameUserCatIn)SELECT nameCatI FROM in_cat;
	 UPDATE user_in_cat SET idUser = $idUser WHERE idUser is null;

	 INSERT INTO user_ex_cat(nameUserCatEx)SELECT nameCatE FROM ex_cat;
	 UPDATE user_ex_cat SET idUser = $idUser WHERE idUser is null; 
	 ";
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 return $stmt->execute();
	 
 }
	
 }//end class