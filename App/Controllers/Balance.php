<?php
namespace App\Controllers;

use \Core\View;
use \App\Models\Transaction;
use \App\Auth;
use \App\Period;

class Balance extends Authenticated{
	
 public function overwievAction(){
	$trans=new Transaction();
	$totalIncomes=$trans->totalIncomes();
	$groupedIncomes=$trans->groupedIncomes();
	$totalExpenses=$trans->totalExpenses();
	$groupedExpenses=$trans->groupedExpenses();
	$total=$totalIncomes['sumI']-$totalExpenses['sumE'];
	
	View::renderTemplate('Logged_in/success.html',['totalI'=>$totalIncomes, 'totalE'=>$totalExpenses,'total'=>$total, 'groupedI'=>$groupedIncomes, 'groupedE'=>$groupedExpenses]);
 }
}//end class;
?>