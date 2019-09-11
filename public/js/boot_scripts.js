window.onload = function () {
	countdown();
}

function countdown(){
	var today = new Date();
	var day = today.getDate();
	if(day<10) day='0'+day;
	var month = today.getMonth()+1;
	if(month<10) month='0'+month;
	var year = today.getFullYear();
	var hour = today.getHours();
	if(hour<10) hour='0'+hour;
	var minute = today.getMinutes();
	if(minute<10) minute='0'+minute;
	var second = today.getSeconds();
	if(second<10) second='0'+second;
	
	document.getElementById("watch").innerHTML=day+'/'+month+'/'+year+"<br>"+hour+':'+minute+':'+second;
	setTimeout("countdown()",1000);
}

$(function() {
  $( "#datepickerFrom" ).datepicker({dateFormat: 'yy-mm-dd' }); 
  $( "#datepickerTo" ).datepicker({ dateFormat: 'yy-mm-dd' });
  $( "#incomeDate" ).datepicker({ dateFormat: 'yy-mm-dd' });
  $( "#expenseDate" ).datepicker({ dateFormat: 'yy-mm-dd' });
  $( "#transactionDate" ).datepicker({ dateFormat: 'yy-mm-dd' });
});
