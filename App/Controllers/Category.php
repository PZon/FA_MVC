<?php
namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\UserCategory;

class Category extends Authenticated{
	
 public function __construct($data=[]){
	foreach($data as $key=>$value){
		$this->$key=$value;
	};
 }

 public function createAction(){
		$category=new UserCategory($_POST);
		$type=$_POST['categoryType'];
	   if($category->saveCategory()){
		Flash::addMessage('New category has been saved.', Flash::WARNING);
		if($type=='UI') $this->redirect('/income/addIncome');
		else $this->redirect('/expense/addExpense');
		exit();
	   }else{
		Flash::addMessage('Error: Category not saved', Flash::WARNING);
		if($type=='UI') $this->redirect('/income/addIncome');
		else $this->redirect('/expense/addExpense');
		}
		exit();
 }
 
 public function verifyCat(){
	$category=$_POST['category'];
	$categoryType=$_POST['categoryType'];
	$catExist=UserCategory::categoryExist($category, $categoryType);
	if($categoryType=='UE'){
		$expLimit=$_POST['expLimit'];
	}
	
  if(isset($category)){

	if($catExist){
		echo '<span class="modalError">Category name already exist!</span>'; 
		exit;
	}
		
	if($categoryType=='UE'){
		if($expLimit < 0 || $expLimit > 100000) {
			echo '<span class="modalError">Value for expenses category should be set between 0 and 100000</span>';
			exit;
		}else if (!empty($expLimit)&&!is_numeric($expLimit)){
			echo '<span class="modalError">Wrong value - not a number<span>';
			exit;
		}
	}
	echo 'NoErrors';
   }
 }
 
 public function showAction(){
	$incomeCat=UserCategory::userIncomeCat();
	$expenseCat=UserCategory::userExpenseCat();
	$paymentCat=UserCategory::userPaymentCat();
	View::renderTemplate('Category/show.html',['incomeCat'=>$incomeCat, 'expenseCat'=>$expenseCat, 'paymentCat'=>$paymentCat]);
		
 }
 
  public function singleUserCat(){
	  $catType=$_POST['catType'];
	  $idCat=$_POST['idCat'];
	  $cat=UserCategory::getUserSingleCat($catType,$idCat);
	  
	  if($cat && $catType=='I'){
		  echo '<p class="text-danger">Category name: '.$cat['nameUserCatIn'].'</p>';
	  }else if($cat && $catType=='E'){
		  echo '<p class="text-danger">Category name: '.$cat['nameUserCatEx'].'</p>';
	  }else if($cat && $catType=='P'){
		  echo '<p class="text-danger">Category name: '.$cat['nameUserCatPay'].'</p>';
	  }else echo 'Error';
  }
  
  public function deleteUserCat(){
	  $catType=$_POST['catType'];
	  $idCat=$_POST['idCat'];
	  
	 if(UserCategory::categoryUsed($catType,$idCat)){
		Flash::addMessage('You can not remove this category. Category links to some transaction - edit transaction first', Flash::WARNING);  
	 }else{
		UserCategory::deleteUserSingleCat($catType,$idCat);
		Flash::addMessage('Category has been removed', Flash::WARNING);		
	  }
  }

 
}//end class;
?>