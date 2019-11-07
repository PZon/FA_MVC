<?php
namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\User;
use \App\Models\Transaction;

class Expense extends \Core\Controller{
	
  public function addExpenseAction(){
	$expenseCat=Transaction::getExpenseCat();
	$paymentCat=Transaction::getPayCat();
	View::renderTemplate('Transaction/addExpense.html',['catsE'=>$expenseCat,'catsP'=>$paymentCat]);
 }

 
}//end class;
?>