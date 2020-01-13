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
   	$("#btnNewCat").click(function(){
	 $("#editCat").toggle('slow');
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
		  url:"/category/createModal",
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
		  url:"/category/createModal",
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
		  url:"/category/createModal",
		  method: "POST",
		  data:{
		    categoryType: categoryType,
			category:category
		  }
		});
	  } 
  });
 });

/***expeses limit verification***/
$(function(){ 
  $.fn.verifyLimit = function(){
	event.preventDefault();
	var amount=$('#amount').val();
	var Category=$('#selectCatE').val();
	if(Category!=0){
		$.ajax({
		  url:"/Expense/verifyCatLimit",
		  method: "POST",
		  data:{
		    amount: amount,
			Category: Category
		  },
		  success:function(data){
		    if(data != 'NoErrors'){
				$("#infoLimit").html('<span class="modalError">'+data+'</span>');
			}
		  }
		});
	}else{
		$("#infoLimit").html('<span class="formWarning">Please choose expense category!</span>');
	}
  }
	$('#amount,#selectCatE').on('input',function(){
        $.fn.verifyLimit();
      });
});

/*** remove category ***/
$(function(){

 $('.ModalI').click(function(){
   var idCat=$(this).attr('id');
   var catType='I';
   
   $.ajax({
    url:"/Category/singleUserCat",
	method: "POST",
	data:{idCat:idCat,
		  catType:catType
	},
	success:function(data){
	 $('#catInfoI').html(data);
	 $('#removeCatIModal').modal("show");
	}
   });
   
   $('.btnRemoveCatI').click(function(){
   $.ajax({
    url:"/Category/deleteUserCat",
	method: "POST",
	data:{idCat:idCat,
		  catType:catType
	},
	success:function(data){
	  $('#removeCatIModal').modal("hide");
	  location.reload();
	}
   });
  });
 });
 
 $('.ModalE').click(function(){
   var idCat=$(this).attr('id');
   var catType='E';
   
   $.ajax({
    url:"/Category/singleUserCat",
	method: "POST",
	data:{idCat:idCat,
		  catType:catType
	},
	success:function(data){
	 $('#catInfoE').html(data);
	 $('#removeCatEModal').modal("show");
	}
   });
   
   $('.btnRemoveCatE').click(function(){
   $.ajax({
    url:"/Category/deleteUserCat",
	method: "POST",
	data:{idCat:idCat,
		  catType:catType
	},
	success:function(data){
	  $('#removeCatEModal').modal("hide");
	  location.reload();
	}
   });
  }); 
 });
 
 $('.ModalP').click(function(){
   var idCat=$(this).attr('id');
   var catType='P';
   
   $.ajax({
    url:"/Category/singleUserCat",
	method: "POST",
	data:{idCat:idCat,
		  catType:catType
	},
	success:function(data){
	 $('#catInfoP').html(data);
	 $('#removeCatPModal').modal("show");
	}
   });
   
   $('.btnRemoveCatP').click(function(){
   $.ajax({
    url:"/Category/deleteUserCat",
	method: "POST",
	data:{idCat:idCat,
		  catType:catType
	},
	success:function(data){
	  $('#removeCatPModal').modal("hide");
	  location.reload();
	}
   });
  }); 
 }); 
});

/***cat settings add new income category***/
$(function(){
 var nrRowI=0;
//dlaczego działa tylko poniższy zapis???
 $('.addIC').on('click', function(){
  nrRowI++
  var html='';
  if(nrRowI==1){
   html+='<tr id="rowI">';
   html+='<td><input type="text" name="cat_name" class="form-control cat_name"/></td>';
   html+='<td> <button type="button" name="removeRowI" class="btn btn-outline-danger mr-sm-1 removeRowI"> <i class="fas fa-minus "></i> </button> <input type="button" id="saveICat" name="submitICat" class="btn btn-outline-success " value="Save" /> </td>';
   html+='</tr>';
   $('.incomeCats').append(html);
  }else{
	$('.addClassErrorI').html('<span class="modalError">Error. Add new category already active. </span>');
  }
 });

	//a tutaj tylko ten???
 $(document).on('click', '.removeRowI', function(){
 //$('.removeRowI').on('click', function(){
 //$('.removeRowI').click( function(){
   $('#rowI').remove();
   nrRowI=0;
   $('.addClassErrorI').html('');
 });

 $(document).on('click','#saveICat', function(){
 //$('#saveICat').click(function(){
	 event.preventDefault();
	 var category=$('.cat_name').val();
	 var categoryType='UI';
	 var validation=false;
	 if(category!=''){
	  $.ajax({
		  url:"/Category/verifyCat",
		  method: "POST",
		  data:{
		    categoryType: categoryType,
			category:category
		  },
		  success:function(data){
		    if(data != 'NoErrors'){
				$(".addClassErrorI").html('<span class="modalError">'+data+'</span>');
			}else if(data == 'NoErrors'){
			  $.ajax({
				url:"/Category/create",
				method: "POST",
				data:{
				 categoryType: categoryType,
				 category:category
				 },
				 success:function(data){
				  location.reload();
	             }
			  });
			}
		  } 
		});
	 }else{
		$('.addClassErrorI').html('<span class="modalError">Category name is required!</span>'); 
	 }
	});
 });
 
 /***cat settings add new expense category***/
$(function(){
 var nrRowE=0;
 $('.addEC').on('click', function(){
  nrRowE++
  var html='';
  if(nrRowE==1){
   html+='<tr id="rowE">';
   html+='<td><input type="text" name="cat_name" class="form-control cat_name"/></td>';
   html+='<td><input type="number" name="catLimit" class="form-control catLimit" min="0" max="100000" placeholder="0"/></td>';
   html+='<td> <button type="button" name="removeRowE" class="btn btn-outline-danger mr-sm-1 removeRowE"> <i class="fas fa-minus "></i> </button> <input type="button" id="saveECat" name="submitECat" class="btn btn-outline-success " value="Save" /> </td>';
   html+='</tr>';
   $('.expenseCats').append(html);
  }else{
	$('.addClassErrorE').html('<span class="modalError">Error. Add new category already active. </span>');
  }
 });

 $(document).on('click', '.removeRowE', function(){
   $('#rowE').remove();
   nrRowE=0;
   $('.addClassErrorE').html('');
 });

 $(document).on('click','#saveECat', function(){
	 event.preventDefault();
	 var category=$('.cat_name').val();
	 var expLimit=$('.catLimit').val();
	 var categoryType='UE';
	 var validation=false;
	 if(category!=''){
	  $.ajax({
		  url:"/Category/verifyCat",
		  method: "POST",
		  data:{
		    categoryType: categoryType,
			category:category,
			expLimit:expLimit
		  },
		  success:function(data){
		    if(data != 'NoErrors'){
				$(".addClassErrorE").html('<span class="modalError">'+data+'</span>');
			}else if(data == 'NoErrors'){
			  $.ajax({
				url:"/Category/create",
				method: "POST",
				data:{
				 categoryType: categoryType,
				 category:category,
				 expLimit:expLimit
				 },
				 success:function(data){
				  location.reload();
	             }
			  });
			}
		  } 
		});
	 }else{
		$('.addClassErrorE').html('<span class="modalError">Category name is required!</span>'); 
	 }
	});
 });
 
/***cat settings add new payment category***/
$(function(){
 var nrRowP=0;
 $('.addPC').on('click', function(){
  nrRowP++
  var html='';
  if(nrRowP==1){
   html+='<tr id="rowP">';
   html+='<td><input type="text" name="cat_name" class="form-control cat_name"/></td>';
   html+='<td> <button type="button" name="removeRowP" class="btn btn-outline-danger mr-sm-1 removeRowP"> <i class="fas fa-minus "></i> </button> <input type="button" id="savePCat" name="submitPCat" class="btn btn-outline-success " value="Save" /> </td>';
   html+='</tr>';
   $('.payCats').append(html);
  }else{
	$('.addClassErrorP').html('<span class="modalError">Error. Add new category already active. </span>');
  }
 });

 $(document).on('click', '.removeRowP', function(){
   $('#rowP').remove();
   nrRowP=0;
   $('.addClassErrorP').html('');
 });

 $(document).on('click','#savePCat', function(){
	 event.preventDefault();
	 var category=$('.cat_name').val();
	 var categoryType='UP';
	 var validation=false;
	 if(category!=''){
	  $.ajax({
		  url:"/Category/verifyCat",
		  method: "POST",
		  data:{
		    categoryType: categoryType,
			category:category
		  },
		  success:function(data){
		    if(data != 'NoErrors'){
				$(".addClassErrorP").html('<span class="modalError">'+data+'</span>');
			}else if(data == 'NoErrors'){
			  $.ajax({
				url:"/Category/create",
				method: "POST",
				data:{
				 categoryType: categoryType,
				 category:category
				 },
				 success:function(data){
				  location.reload();
	             }
			  });
			}
		  } 
		});
	 }else{
		$('.addClassErrorP').html('<span class="modalError">Category name is required!</span>'); 
	 }
	});
 });



