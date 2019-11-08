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
	$trans=new Transaction();
	$incomeCat=$trans->getIncomeCat();
	$expenseCat=$trans->getExpenseCat();
	$paymentCat=$trans->getPayCat();
	
	if($this->view=='cm'){
	 $incomes=$trans->getIncomesCM();
	 View::renderTemplate('Statement/statement.html',['catsI'=>$incomeCat,'catsE'=>$expenseCat,'catsP'=>$paymentCat, 'incomes'=>$incomes, 'view'=>$this->view]);
	}else if($this->view=='pm'){
		$prevYM=Period::setPreviousYM();
		$prevYMEnd=Period::setPreviousYMEnd();
		
		$incomes=$trans->getIncomesPM();
		View::renderTemplate('Statement/statement.html',['catsI'=>$incomeCat,'catsE'=>$expenseCat,'catsP'=>$paymentCat, 'incomes'=>$incomes, 'view'=>$this->view]);
	}else if($this->view=='cp'){
		$prevYM=Period::setPreviousYM();
		$prevYMEnd=Period::setPreviousYMEnd();
		
		$incomes=$trans->getIncomesPM();
		View::renderTemplate('Statement/statement.html',['catsI'=>$incomeCat,'catsE'=>$expenseCat,'catsP'=>$paymentCat, 'incomes'=>$incomes, 'view'=>$this->view]);
	};
 }
 
}//end class;
?>