<?php
namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Transaction;

class Expense extends \Core\Controller{
	
 function __construct(){
   $this->expenseCat=Transaction::getExpenseCat();
   $this->paymentCat=Transaction::getPayCat();
 }
	
  public function addExpenseAction(){
	View::renderTemplate('Transaction/addExpense.html',['catsE'=>$this->expenseCat,'catsP'=>$this->paymentCat]);
 }
 
 public function createAction(){
	$expense=new Transaction($_POST);
   if($expense->saveTransaction()){
	Flash::addMessage('New expense has been saved.', Flash::WARNING);
	$this->redirect('/expense/addExpense');
	exit();
   }else{
	View::renderTemplate('Transaction/AddExpense.html',['expense'=>$expense,'catsE'=>$this->expenseCat,'catsP'=>$this->paymentCat]);  
   }
 }

 
}//end class;
?>