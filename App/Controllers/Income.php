<?php
namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Period;
use \App\Models\Transaction;

class Income extends Authenticated{
	
 function __construct(){
   $this->incomeCat=Transaction::getIncomeCat();
 }

  public function addIncomeAction(){
	View::renderTemplate('Transaction/addIncome.html',['catsI'=>$this->incomeCat]);
 }
 public function createAction(){
	$income=new Transaction($_POST);
   if($income->saveTransaction()){
	Flash::addMessage('New income has been saved.', Flash::WARNING);
	$this->redirect('/income/addIncome');
	exit();
   }else{
	View::renderTemplate('Transaction/addIncome.html',['income'=>$income,'catsI'=>$this->incomeCat]);  
   }
 }
 
 public function showSingleIncome(){
	 if(isset($_POST['incomeId'])){
	 $_SESSION['incomeId']=$_POST['incomeId'];
	 }
	 $income=Transaction::getSingleTransaction();
	 View::renderTemplate('Transaction/displayIncome.html',['income'=>$income]); 
 }
 
  public function editIncome(){
	 $income=Transaction::getSingleTransaction();
	 View::renderTemplate('Transaction/editIncomeForm.html',['catsI'=>$this->incomeCat,'income'=>$income]); 
 }
 
   public function deleteIncome(){

	if(Transaction::deleteTransaction()){
		Flash::addMessage('Transaction has been removed');
		$this->redirect('/statement/displayStatement?view=cm');
	}else{
		$income=Transaction::getSingleTransaction();
		Flash::addMessage('Error');
		View::renderTemplate('Transaction/editIncomeForm.html',['catsI'=>$this->incomeCat,'income'=>$income]);
	}
 }
 
 public function updateIncome(){
   if(Transaction::updateTransaction()){
	Flash::addMessage('Income data has been updated.', Flash::WARNING);
	$this->redirect('/statement/displayStatement?view=cm');
	exit();
   }else{
	$income=Transaction::getSingleTransaction();
	Flash::addMessage('Error');
	View::renderTemplate('Transaction/editIncomeForm.html',['catsI'=>$this->incomeCat,'income'=>$income]);  
   }
 }  
 
}//end class;
?>