<?php
namespace App\Controllers;

namespace App\Controllers;

use \Core\View;
use \App\Models\Transaction;
use \App\Auth;
use \App\Period;

class Statement extends Authenticated{
	
 function __construct(){
   $this->view=$_GET['view'];
 }

 public function displayStatementAction(){
	unset($_SESSION['incomeId']);
	unset($_SESSION['expenseId']);
	$trans=new Transaction();
	$incomeCat=$trans->getIncomeCat();
	$expenseCat=$trans->getExpenseCat();
	$paymentCat=$trans->getPayCat();
	
	if($this->view=='cm'){
	 $incomes=$trans->getIncomesCM();
	 $expenses=$trans->getExpensesCM();
	 
	 View::renderTemplate('Statement/statement.html',['catsI'=>$incomeCat,'catsE'=>$expenseCat,'catsP'=>$paymentCat, 'incomes'=>$incomes, 'expenses'=>$expenses, 'view'=>$this->view]);
	}else if($this->view=='pm'){
		$incomes=$trans->getIncomesPM();
		$expenses=$trans->getExpensesPM();
		
		View::renderTemplate('Statement/statement.html',['catsI'=>$incomeCat,'catsE'=>$expenseCat,'catsP'=>$paymentCat, 'incomes'=>$incomes,'expenses'=>$expenses, 'view'=>$this->view]);
	}else if($this->view=='cp'){
		if(!isset($_GET['dateFrom'])){
			View::renderTemplate('Statement/statement.html',['catsI'=>$incomeCat,'catsE'=>$expenseCat,'catsP'=>$paymentCat, 'view'=>$this->view]);
		}else{
		$incomes=$trans->getIncomesCP($_GET['dateFrom'], $_GET['dateTo']);
		$expenses=$trans->getExpensesCP($_GET['dateFrom'], $_GET['dateTo']);
			View::renderTemplate('Statement/statement.html',['catsI'=>$incomeCat,'catsE'=>$expenseCat,'catsP'=>$paymentCat,'incomes'=>$incomes,'expenses'=>$expenses, 'view'=>$this->view]);
		}
	};
 }
 
}//end class;
?>