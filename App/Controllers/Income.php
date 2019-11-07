<?php
namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\User;
use \App\Models\Transaction;

class Income extends \Core\Controller{
  public function addIncomeAction(){
	$incomeCat=Transaction::getIncomeCat();
	View::renderTemplate('Transaction/addIncome.html',['catsI'=>$incomeCat]);
 }

 
}//end class;
?>