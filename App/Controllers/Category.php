<?php
namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\UserCategory;

class Category extends Authenticated{
	
 function __construct(){

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
	Flash::addMessage('Error: Category not saved.', Flash::WARNING);
	if($type=='UI') $this->redirect('/income/addIncome');
	else $this->redirect('/expense/addExpense');
   }
 }
 
}//end class;
?>