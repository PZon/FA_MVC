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
/*** Date picker ***/
$(function() {
  $( "#datepickerFrom" ).datepicker({dateFormat: 'yy-mm-dd' }); 
  $( "#modalDatepickerFrom" ).datepicker({dateFormat: 'yy-mm-dd' }); 
  $( "#datepickerTo" ).datepicker({dateFormat: 'yy-mm-dd' });
  $( "#modalDatepickerTo" ).datepicker({dateFormat: 'yy-mm-dd' });
  $( "#incomeDate" ).datepicker({dateFormat: 'yy-mm-dd' });
  $( "#expenseDate" ).datepicker({dateFormat: 'yy-mm-dd' });
  $( "#transactionDate" ).datepicker({dateFormat: 'yy-mm-dd' });
});

/*** Expense Limit ***/
 $(function(){
	$("#addExpenseLimit").click(function(){
	 $("#expenseLimit").toggle('slow');
	});
   });
   

$(function(){
/*** Expense Cat Modal Validation ***/	
 $('#btnExCat').click(function(){
 event.preventDefault();
	var categoryType=$('#catT').val();
	var category=$('#exCat').val();
	var expLimit=$('#expenseLimit').val();
	var validation=true;

	  if(category!=''){
		$.ajax({
		  url:"/category/verifyCat",
		  method: "POST",
		  data:{
		    categoryType: categoryType,
			category:category,
			expLimit:expLimit
		  },
		  success:function(data){
		    if(data != 'NoErrors'){
				$("#infoCat").html('<span class="modalError">'+data+'</span>');
				validation=false;
			}else{
			  $('#addExpenseCatModal').hide();
			  location.reload();
			  validation=true;
			}
		  }
		});
	  }else{
		$("#infoCat").html('<span class="modalError">Category name is required!</span>');
		validation=false;
	  }
	  
	  if(validation==true){
		$.ajax({
		  url:"/category/create",
		  method: "POST",
		  data:{
		    categoryType: categoryType,
			category:category,
			expLimit:expLimit
		  }
		});
	  }  
 });
 /*** Payment type Modal Validation ***/
 $('#btnPayCat').click(function(){
 event.preventDefault();
	var categoryType=$('#categoryType').val();
	var category=$('#paymentCategory').val();
	var validation=true;
	  if(category!=''){
		$.ajax({
		  url:"/category/verifyCat",
		  method: "POST",
		  data:{
		    categoryType: categoryType,
			category:category
		  },
		  success:function(data){
		    if(data != 'NoErrors'){
				$("#infoCatP").html('<span class="modalError">'+data+'</span>');
				validation=false;
			}else if(data == 'NoErrors'){
			  validation=true;
			  $('#addPaymentTypeModal').modal('hide');
			  location.reload();
			}
		  }
		});
	  }else{
		$("#infoCatP").html('<span class="modalError">Category name is required!</span>');
		validation=false;
	  }
	  
	  if(validation==true){
	    $.ajax({
		  url:"/category/create",
		  method: "POST",
		  data:{
		    categoryType: categoryType,
			category:category
		  }
		});
	  } 
 });
 
 /*** Income Cat Modal Validation ***/	
 $('#btnIncCat').click(function(){
	event.preventDefault();
	var categoryType=$('#categoryType').val();
	var category=$('#incomeCategory').val();
	var validation=true;
	  if(category!=''){
		$.ajax({
		  url:"/category/verifyCat",
		  method: "POST",
		  data:{
		    categoryType: categoryType,
			category:category
		  },
		  success:function(data){
		    if(data != 'NoErrors'){
				$("#infoCatI").html('<span class="modalError">'+data+'</span>');
				validation=false;
			}else if(data == 'NoErrors'){
			  validation=true;
			  $('#addIncomeCatModal').modal('hide');
			  location.reload();
			}
		  }
		});
	  }else{
		$("#infoCatI").html('<span class="modalError">Category name is required!</span>');
		validation=false;
	  }
	  
	  if(validation==true){
	    $.ajax({
		  url:"/category/create",
		  method: "POST",
		  data:{
		    categoryType: categoryType,
			category:category
		  }
		});
	  } 
 });
 
 
});	


