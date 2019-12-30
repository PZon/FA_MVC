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
	 $sqlE="SELECT idCatE, nameCatE FROM ex_cat WHERE nameCatE = :catName
	 UNION 
	 SELECT idUserCatEx, nameUserCatEx FROM user_ex_cat 
	 WHERE idUser={$_SESSION['idUser']} AND nameUserCatEx = :catName ";
	 $sqlI="SELECT idCatI, nameCatI FROM in_cat WHERE nameCatI=:catName 
	 UNION 
	 SELECT idUserCatIn, nameUserCatIn FROM user_in_cat WHERE idUser={$_SESSION['idUser']} AND nameUserCatIn=:catName ";
	 $sqlP="SELECT idCatPay, nameCatPay FROM pay_cat WHERE nameCatPay=:catName
	 UNION 
	 SELECT idUserCatPay, nameUserCatPay FROM user_pay_cat 
	 WHERE idUser= {$_SESSION['idUser']} AND nameUserCatPay=:catName";
	 $db=static::getDB();
	 
	 if($type=='UI'){
		$stmt=$db->prepare($sqlI);
	 }else if($type=='UE'){
		$stmt=$db->prepare($sqlE);
	 }else if($type=='UP'){
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
 
 public function getUserExpCat(){
	 $sql="SELECT * FROM user_ex_cat WHERE idUser={$_SESSION['idUser']}";
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
	 $stmt->execute();
	 $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
     return $result;
 }
	
 }//end class