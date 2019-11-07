<?php
namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\User;
use \App\Models\Transaction;

class Income extends \Core\Controller{
	
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
	 Flash::addMessage('Dupa', Flash::WARNING);
	View::renderTemplate('Transaction/addIncome.html',['income'=>$income,'catsI'=>$this->incomeCat]);  
   }
 }

 
}//end class;
?>