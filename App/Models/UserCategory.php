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
	$this->category=filter_input(INPUT_POST,'category',FILTER_SANITIZE_STRING);
	 $category=strtoupper($this->category);
	 if($this->categoryType=='UI'){
		$sql="INSERT INTO user_in_cat VALUES (NULL, {$_SESSION['idUser']}, :category)";
	 }else if($this->categoryType=='UE'){
		$sql="INSERT INTO user_ex_cat VALUES (NULL, {$_SESSION['idUser']}, :category)";
	 }else if($this->categoryType=='UP'){
		$sql="INSERT INTO user_pay_cat VALUES (NULL, {$_SESSION['idUser']}, :category)";
	 }
	 $db=static::getDB();
	 $stmt=$db->prepare($sql);
	 
	 $stmt->bindValue(':category', $category, PDO::PARAM_STR );
	 
	 return $stmt->execute();
	}
	return false;
 }
 
 public function validate(){
	 if ($this->category == '') {
           $this->errors[] = 'Category name is required';
       }
 }
	
 }//end class