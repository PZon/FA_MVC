<?php
namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Transaction;

class Expense extends Authenticated{
	
 function __construct(){
   $this->expenseCat=Transaction::getExpenseCat();
   $this->paymentCat=Transaction::getPayCat();
 }
	
  public function addExpenseAction(){
	View::renderTemplate('Transaction/AddExpense.html',['catsE'=>$this->expenseCat,'catsP'=>$this->paymentCat]);
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
 
 public function showSingleExpense(){
	 if(isset($_POST['expenseId'])){
	 $_SESSION['expenseId']=$_POST['expenseId'];
	 }
	 $expense=Transaction::getSingleTransaction();
	 View::renderTemplate('Transaction/displayExpense.html',['expense'=>$expense]); 
 }
 
 public function deleteExpense(){

	if(Transaction::deleteTransaction()){
		Flash::addMessage('Transaction has been removed');
		$this->redirect('/statement/displayStatement?view=cm');
	}else{
		$income=Transaction::getSingleTransaction();
		Flash::addMessage('Error');
		View::renderTemplate('Transaction/editIncomeForm.html',['expense'=>$expense, 'catsE'=>$this->expenseCat,'catsP'=>$this->paymentCat]);
	}
 }
 
   public function editExpense(){
	 $expense=Transaction::getSingleTransaction();
	 View::renderTemplate('Transaction/editExpenseForm.html',['expense'=>$expense, 'catsE'=>$this->expenseCat,'catsP'=>$this->paymentCat]); 
 }
 
 public function updateExpense(){
   if(Transaction::updateTransaction()){
	Flash::addMessage('Expense data has been updated.', Flash::WARNING);
	$this->redirect('/statement/displayStatement?view=cm');
	exit();
   }else{
	$income=Transaction::getSingleTransaction();
	Flash::addMessage('Error');
	View::renderTemplate('Transaction/editExpenseForm.html',['expense'=>$expense, 'catsE'=>$this->expenseCat,'catsP'=>$this->paymentCat]);  
   }
 }  

 
}//end class;
?>