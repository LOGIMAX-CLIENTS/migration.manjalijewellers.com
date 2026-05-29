$(document).ready(function() {

//due count	
	var accData = [];
	
	// on selecting chit scheme
    $("input[name=sch_all]").on("click",function(ev)
	{
	  var payment_amount=0;
	  var id=$(this).parent().parent().find(".id_scheme_account").val();
		$("#scheme-amount tbody tr").each(function(index, value) 
		{
		//alert("#select_id_"+id);
		 if($(value).find("#select_id_"+id).is(":checked"))
		 {
		   var pid =parseFloat(id);
		   my_Date = new Date();
			$('.overlay').css('display','block');
		   //get chit scheme content
		  $.ajax({
			type: "GET",
			url: baseURL+"index.php/paymt/getPaymentContent/"+pid+"?nocache=" + my_Date.getUTCSeconds(),
			dataType: "json",
			cache: false,
			success: function(data) {
				$('.overlay').css('display','none');
		accData = data.chit;
		 if(accData.scheme_type==0)
		 {		
			  var due_no = parseInt(accData.paid_installments) + 1;
//calculate discount
			  var calc_discount = (accData.firstPayDisc_by==1?accData.discount:(parseFloat(accData.payable)*(parseFloat(accData.discount)/100)).toFixed(2));
// check discount settings to calculate discount
					 
			 if(accData.allowPayDisc==1){
				var amount =(parseFloat(accData.payable) - parseFloat(calc_discount)).toFixed(0);
				var discountedAmt = parseFloat(calc_discount).toFixed(0);
			 }
			 else{
				var amount = parseFloat(accData.payable);
				var discountedAmt = "";
			 } 
	 // calculate gateway charge
	 
			 var charge = 0.00;
			 if(accData.charge_type == 0){
				console.log(charge);
			   charge = ((parseFloat(amount)*(parseFloat(accData.charge)/100)).toFixed(0));
			 }
			 else if(accData.charge_type == 1){
			  charge = accData.charge;
			 }
			 
			
			 
// Gst calculate	 
			 /*accData.gst_type =0;
			  accData.gst =5;*/
			   var calc_gst = ((parseFloat(accData.payable)*(parseFloat(accData.gst)/100)).toFixed(0));
			   
			if(accData.gst_type==0){
				var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));}
				
			if(accData.gst_type==1){	
				var payment_amt = Math.round((parseFloat(amount)+parseFloat(calc_gst))+parseFloat(charge));}
				/*alert(accData.gst_type);*/
			 $(value).find(".payment_amt").val(payment_amt);
			 $(value).find(".amount").val(amount);
			 $(value).find(".gst_val").val(calc_gst);
			 $(value).find(".gst_type").val(accData.gst_type);
			 $(value).find(".charge").val(charge);
			 $(value).find(".discount").val(discountedAmt);
			 $(value).find(".ischecked").val(1);
			 $(value).find(".no_of_due").val(due_no);
			 $(value).find(".sel_due").val(1)
			 $(value).find(".allowed_dues").val(accData.allowed_dues)
			 $(value).find(".payable").val(accData.payable);
			 $(value).find(".actamt").val(accData.payable);
			 $(value).find(".allowPayDisc").val(accData.allowPayDisc);
			 var show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(payment_amt)+'</b><br>';
			if(charge > 0){
				 show_pay = show_pay +'<span style="font-size: 11px;">('+(amount)+' + '+parseFloat(charge)+'*)</span></br>';
			 }		
			 if(discountedAmt >0){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>"
			 }
// Gst calculate	
			if(calc_gst >0 && accData.gst_type==0){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt inclusive : "+parseFloat(calc_gst)+"</span>"}
			 
			 if(calc_gst >0 && accData.gst_type==1){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt exclusive : "+parseFloat(calc_gst)+"</span>"}
						 
			 $(value).find(".show_pay").html(show_pay);	
			
			  //calculate total selected payment amount
			  var totamt = 0.00;
			 
			 $("#scheme-amount tbody tr").each(function(index, value) 
			 {
				 if($(value).find(".select_chit").is(":checked"))
				 {
					if(parseFloat($(value).find('.discount').val())>0){
						//console.log(parseFloat(totamt));
						totamt =(parseFloat(totamt)+((((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))-parseFloat($(value).find('.discount').val()))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0)));
					}else{
						totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0)));
					}
				 }
			});
			
			 $('#tot_amt').val(totamt);
			 $('#tot_sel_amt').html(totamt); 
	 }
	 else if(accData.scheme_type==1 && (accData.max_weight!=accData.min_weight)){
		 
		 var due_no = parseInt(accData.current_paid_installments) + 1;
		 var paycontent = payment.setcontent(data);
		 var payment_amount=$('#tot_amt').val(); 
		 $(value).find(".sel_due").val(1);
		 $(value).find(".allowed_dues").val(accData.allowed_dues);
		 if(paycontent){
			$('#payModal .modal-body').html(paycontent);
			$('#payModal').modal('show', {backdrop: 'static'});
		 }
	} 
	else if(accData.scheme_type==1 && (accData.max_weight==accData.min_weight)){
						
		 var weight = data.weights;
		 var rate  = data.metal_rates;					
		 var due_no = parseInt(accData.current_paid_installments) + 1;
		 var selweight=$(value).find(".sel_weight").val();
	  
	  //calculate discount
		  var calc_discount = (accData.firstPayDisc_by==1?accData.discount:((parseFloat(accData.payable)*parseFloat(rate.goldrate_22ct))*(parseFloat(accData.discount)/100)).toFixed(2));
		  
	 // check discount settings to calculate discount
		 if(accData.allowPayDisc==1){

			var amount =(parseFloat(accData.max_weight*rate.goldrate_22ct) - parseFloat(calc_discount)).toFixed(2);
			var discountedAmt = parseFloat(calc_discount).toFixed(2);
		 }
		 else{
			
			var amount = parseFloat(accData.max_weight*rate.goldrate_22ct);
			var discountedAmt = "";
		 }
		
	// calculate gateway charge
		 var charge = 0.00;
		 if(accData.charge_type == 0 && accData.scheme_type==1 && (accData.max_weight==accData.min_weight)){
		   var charge = ((parseFloat(amount)*(parseFloat(accData.charge)/100)).toFixed(2));
		 }
		 else if(accData.charge_type == 1 && accData.scheme_type==1 && (accData.max_weight==accData.min_weight)){
		   var charge = accData.charge;
		 }
// Gst calculate	 
			/* accData.gst_type =1;
			  accData.gst =5;*/
			   var calc_gst = ((parseFloat(accData.max_weight*rate.goldrate_22ct)*(parseFloat(accData.gst)/100)).toFixed(0));
			   
			if(accData.gst_type==0){
				var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));}
				
			if(accData.gst_type==1){	
		
				var payment_amt = Math.round((parseFloat(amount)+parseFloat(calc_gst))+parseFloat(charge));}
		 
		// var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));				
	
		var show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(payment_amt)+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+parseFloat(accData.max_weight).toFixed(1)+' gm </span></br>';
		
		 if(charge > 0){
			 show_pay = show_pay +'<span style="font-size: 11px;">('+parseFloat(amount).toFixed(0)+' + '+parseFloat(charge)+'*)</span></br>';
		 }
		
		 if(discountedAmt >0){
		 show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>";
		 }
		 
		// Gst calculate	
			/* if(calc_gst >0 && accData.gst_type==0){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt inclusive : "+parseFloat(calc_gst)+"</span>"} */
			 
			 if(calc_gst >0 && accData.gst_type==1){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt exclusive : "+parseFloat(calc_gst)+"</span>"} 
		 
		 
						 
		 $(value).find(".show_pay").html(show_pay);	
		 $(value).find(".payment_amt").val(payment_amt);
		 $(value).find(".amount").val(amount);
		 $(value).find(".gst_val").val(calc_gst);
		 $(value).find(".gst_type").val(accData.gst_type);
		 $(value).find(".ischecked").val(1);
		 $(value).find(".sel_weight").val(accData.max_weight);
		 $(value).find(".metal_rate").val(rate.goldrate_22ct);
		 $(value).find(".no_of_due").val(due_no);
		 $(value).find(".sel_due").val(1);
		 $(value).find(".discount").val(discountedAmt);
		 $(value).find(".allowed_dues").val(accData.allowed_dues);
		 $(value).find(".charge").val(charge);
		 $(value).find(".payable").val(accData.payable);
		 $(value).find(".actamt").val(parseFloat(accData.max_weight*rate.goldrate_22ct));
		 $(value).find(".allowPayDisc").val(accData.allowPayDisc);
		//calculate total selected payment amount
		  var totamt = 0.00;
						 
		 $("#scheme-amount tbody tr").each(function(index, value) 
		 {
			 if($(value).find(".select_chit").is(":checked"))
			 {
				if(parseFloat($(value).find('.discount').val()) >0){
					totamt =(parseFloat(totamt)+((((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))-parseFloat($(value).find('.discount').val()))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0)));
				}else{
					totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0)));
				}
			 }
		 })
		
		 $('#tot_amt').val(totamt);
		 $('#tot_sel_amt').html(totamt);
		 
   }
	else if(accData.scheme_type==2)
	{
		var metal_rate  = data.metal_rates.goldrate_22ct;
		if(metal_rate != ''){
			var selweight = accData.payable/metal_rate;
		}
		else{
			var selweight = 0;
			
		}
		var due_no = parseInt(accData.current_paid_installments) + 1;
					
	 //calculate discount
		  var calc_discount = (accData.firstPayDisc_by==1?accData.discount:(parseFloat(accData.payable)*(parseFloat(accData.discount)/100)).toFixed(2));
	 // check discount settings to calculate discount
		 if(accData.allowPayDisc==1){
			var amount =(parseFloat(accData.payable) - parseFloat(calc_discount)).toFixed(2);
			var discountedAmt = parseFloat(calc_discount).toFixed(2);
		 }
		 else{
			var amount = parseFloat(accData.payable);
			var discountedAmt = "";
		 } 
		 
		// calculate gateway charge
			 var charge = 0.00;
			 if(accData.charge_type == 0 && accData.scheme_type != 1){
			   var charge = ((parseFloat(amount)*(parseFloat(accData.charge)/100)).toFixed(2));
			 }
			 else if(accData.charge_type == 1 && accData.scheme_type != 1){
			   var charge = accData.charge;
			 }
	 // Gst calculate	 
			 /*accData.gst_type =0;
			  accData.gst =5;*/
			   var calc_gst = ((parseFloat(accData.payable)*(parseFloat(accData.gst)/100)).toFixed(0));
			   
			if(accData.gst_type==0){
				var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));}
				
			if(accData.gst_type==1){	
				var payment_amt = Math.round((parseFloat(amount)+parseFloat(calc_gst))+parseFloat(charge));}
			// var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));
		var show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(payment_amt)+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+parseFloat(selweight).toFixed(1)+' gm </span></br>';
			 if(charge > 0){
				 show_pay = show_pay +'<span style="font-size: 11px;">('+parseFloat(amount)+' + '+parseFloat(charge)+'*)</span></br>';
			 }
			
			 if(discountedAmt >0){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>";
			 }
 //Gst calculate
			if(calc_gst >0 && accData.gst_type==0){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt inclusive : "+parseFloat(calc_gst)+"</span>"}
			 
			 if(calc_gst >0 && accData.gst_type==1){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt exclusive : "+parseFloat(calc_gst)+"</span>"}
			 
			$(value).find(".show_pay").html(show_pay)	
			$(value).find(".payment_amt").val(payment_amt);
			$(value).find(".amount").val(amount);
			$(value).find(".gst_val").val(calc_gst);
			$(value).find(".gst_type").val(accData.gst_type);
			$(value).find(".sel_weight").val(parseFloat(selweight).toFixed(1));
			$(value).find(".metal_rate").val(metal_rate);
			$(value).find(".charge").val(charge);
			 $(value).find(".discount").val(discountedAmt);
			$(value).find(".ischecked").val(1);
			$(value).find(".no_of_due").val(due_no);
			$(value).find(".sel_due").val(1);
			$(value).find(".allowed_dues").val(accData.allowed_dues);
			$(value).find(".actamt").val(accData.payable);
			$(value).find(".allowPayDisc").val(accData.allowPayDisc);			
			 var totamt = 0.00;
			 
			 $("#scheme-amount tbody tr").each(function(index, value) 
			 {
				 if($(value).find(".select_chit").is(":checked"))
				 {
					if(parseFloat($(value).find('.discount').val()) >0){
						totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))-parseFloat($(value).find('.discount').val()))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0));
					}else{
					totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0)));
					}
				 }
			 });
		 //console.log(parseFloat($(value).find('.payment_amt').val()));
		 $('#tot_amt').val(totamt);
		 $('#tot_sel_amt').html(totamt);
		}
	  }
	});//end of ajaxcall
	
}

			
});//end of loop
	
	  var totamt = 0.00;
	 $("#scheme-amount tbody tr").each(function(index, value) 
		{
		 if(!$(value).find(".select_chit").is(":checked"))
		 {

			$(value).find(".show_pay").empty();
			$(value).find(".ischecked").val(0);
			$(value).find(".payment_amt").val(0.00);
			$(value).find(".gst_val").val(0.00);
			$(value).find(".amount").val(0.00);
			$(value).find(".charge").val(0.00);
			$(value).find(".discount").val(0.00);
			$(value).find(".on_of_due").val(0);
			$(value).find(".actamt").val(0);
			$(value).find(".sel_due").val(1);
			$(value).find(".allowPayDisc").val(0);
			$(value).find("#tot_amt").val(0.00);
		 }
		 else if($(value).find(".select_chit").is(":checked"))
		 {
		 	if(parseFloat($(value).find('.discount').val())>0){
				 totamt =(parseFloat(totamt)+((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))-parseFloat($(value).find('.discount').val())+($(value).find('.gst_type').val()==1?((parseFloat($(value).find('.sel_due').val()))*parseFloat($(value).find('.gst_val').val())):0));
			}else{
				//totamt = parseFloat(totamt) + (parseFloat($(value).find('.payment_amt').val())*parseFloat($(value).find('.sel_due').val())+($(value).find('.gst_type').val()==1?((parseFloat($(value).find('.sel_due').val()))*parseFloat($(value).find('.gst_val').val())):0));
				
				totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0)));
				
			}
		  }
		 $('#tot_amt').val(totamt);
		 $('#tot_sel_amt').html(totamt); 
	}); 
 });
	
	$(document.body).on('click', '.chg_wgt' ,function(){
		payment.changeWgt($(this).val());
	});
		
 $(document).on('click', '.dec_due', function(e){
	var dec_due_id = ($(this).val());
	 var totamt = 0.00;
	    $("#scheme-amount tbody tr").each(function(index, value)
		{
	    	if((index+1) == dec_due_id)
			 {	 
				var sel_due = parseFloat($(value).find(".sel_due").val()) ; var discountedAmt = parseFloat($(value).find(".discount").val());		
				if(sel_due > 1)
				 {
					var due_count= (sel_due-1);						
					$(value).find(".sel_due").val(due_count);
					var charge = parseFloat($(value).find(".charge").val());
					var actamt = parseFloat($(value).find(".actamt").val());
					var amount = parseFloat($(value).find(".amount").val());
					var pay_amt = ((amount*due_count) + (charge*due_count));
					
		// Gst calculate
			  
				if($(value).find(".gst_type").val()==1 && pay_amt>0){
				var pay_amt = parseFloat(pay_amt)+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()));}
					
				 //added				
					if($(value).find('#scheme_type').val() == 1 && $(value).find('#is_flexible_wgt').val() == 0)
					{
						var selweight=$(value).find(".sel_weight").val();
						var charge=parseFloat($(value).find(".charge").val())*due_count;
						
							 show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(pay_amt).toFixed(0)+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+parseFloat(selweight*due_count).toFixed(1)+' gm </span></br>';
					
						 if(charge > 0){
						 	 show_pay = show_pay +'<span style="font-size: 11px;">('+parseFloat(discountedAmt>0?(actamt*due_count)-parseFloat(discountedAmt):amount*due_count).toFixed(0)+' + '+charge+'*)</span></br>';
						 }
						
						 if(discountedAmt >0){
						 show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>";
						 }
						 
			// Gst Calculations		
					
				if($(value).find(".gst_val").val() >0 && $(value).find(".gst_type").val()==1){
				 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt exclusive : "+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()))+"</span>"} 
						 
						 
											 
						$(value).find(".show_pay").html(show_pay);
				}
				else if($(value).find('#scheme_type').val() == 2)
				{
					var total_amt = amount;
					var metal_rate = parseFloat($(value).find('.metal_rate').val());
					var charge = parseFloat($(value).find('.charge').val())*due_count;
					
					if(total_amt != '' && metal_rate != ''){
						var selweight = total_amt/metal_rate;
					}
				
					show_pay = '<b>'+accData.currency_symbol+' '+pay_amt+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+parseFloat((selweight*due_count)).toFixed(2)+' gm </span></br>';
					
					if(charge > 0){
						 show_pay = show_pay +'<span style="font-size: 11px;">('+(discountedAmt>0?(actamt*due_count)-parseFloat(discountedAmt):amount*due_count)+' + '+charge+'*)</span></br>';
					 }
					 
					 if(discountedAmt >0){
					 show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>"
					 }
					 
			 // Gst calculate
		
			if($(value).find(".gst_val").val()>0 && $(value).find(".gst_type").val()==0){
				 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt inclusive : "+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()))+"</span>"}
			 
			 if($(value).find(".gst_val").val() >0 && $(value).find(".gst_type").val()==1){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt exclusive : "+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()))+"</span>"}
					 
					$(value).find(".show_pay").html(show_pay);
			 }
			 else{
//end of added
			show_pay = '<b>'+accData.currency_symbol+' '+pay_amt+'</b><br>';
			 if(charge > 0){
				 show_pay = show_pay +'<span style="font-size: 11px;">('+(amount*due_count)+' + '+(charge*due_count)+'*)</span></br>';
			 }
			 if(discountedAmt >0){
				 show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>"
				 }
				 
		// Gst calculate
		
			if($(value).find(".gst_val").val()>0 && $(value).find(".gst_type").val()==0){
				 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt inclusive : "+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()))+"</span>"}
			 
			 if($(value).find(".gst_val").val() >0 && $(value).find(".gst_type").val()==1){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt exclusive : "+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()))+"</span>"}
			 
			$(value).find(".show_pay").html(show_pay);
		  }
		}
	  }
	});		
		
	$("#scheme-amount tbody tr").each(function(index, value) 
	 {
							 
		if(!$(value).find(".select_chit").is(":checked"))
		 {
			$(value).find(".show_pay").empty();
			$(value).find(".ischecked").val(0);
			$(value).find(".payment_amt").val(0.00);
			$(value).find(".gst_val").val(0.00);
			$(value).find(".amount").val(0.00);
			$(value).find(".charge").val(0.00);
			$(value).find(".discount").val(0.00);
			$(value).find(".on_of_due").val(0);
			$(value).find(".actamt").val(0);
			$(value).find("#tot_sel_amt").val(0.00);
			/*alert($('#tot_amt').val());*/
		 } 
		else if($(value).find(".select_chit").is(":checked"))
		 {
			if(parseFloat($(value).find('.discount').val()) >0 ){
				//console.log(parseFloat(totamt));
				totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))-parseFloat($(value).find('.discount').val())+($(value).find('.gst_type').val()==1?((parseFloat($(value).find('.sel_due').val()))*parseFloat($(value).find('.gst_val').val())):0)));
			}else{
				totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0)));
			}
		 }
	 });
		 $('#tot_amt').val(totamt);
		 $('#tot_sel_amt').html(totamt);
  });
    
    $(document).on('click', '.incre_due', function(e){
		var incre_due_id = ($(this).val());
		 var totamt = 0.00;
	    $("#scheme-amount tbody tr").each(function(index, value)
		{
	    	if((index+1) == incre_due_id)
			{
				var sel_due = parseFloat($(value).find(".sel_due").val());
				var allowed_dues = parseFloat($(value).find(".allowed_dues").val()); 	
				if(sel_due < allowed_dues)
				{
					var due_count = (sel_due+1);						
					$(value).find(".sel_due").val(due_count);
					var charge = parseFloat($(value).find(".charge").val());
					var amount = parseFloat($(value).find(".amount").val());
					var actamt = parseFloat($(value).find(".actamt").val());
					var gst_type = parseFloat($(value).find(".gst_type").val());
					var discountedAmt = parseFloat($(value).find(".discount").val());
					var pay_amt = accData.firstPayDisc!=1?((amount*due_count) + (charge*due_count)):(actamt*due_count)+(charge*due_count)-discountedAmt;
	
	// Gst calculate
			  
				if($(value).find(".gst_type").val()==1 && pay_amt>0){
				var pay_amt = parseFloat(pay_amt)+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()));}
				
					if($(value).find('#scheme_type').val() == 1 && $(value).find('#is_flexible_wgt').val() == 0)
					{
						var selweight=$(value).find(".sel_weight").val();
						var charge=parseFloat($(value).find(".charge").val())*due_count;
					
						 show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(pay_amt).toFixed(0)+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+parseFloat(selweight*due_count).toFixed(1)+' gm </span></br>';
				
					 if(charge > 0){
						 show_pay = show_pay +'<span style="font-size: 11px;">('+parseFloat(discountedAmt>0?(actamt*due_count)-parseFloat(discountedAmt):amount*due_count).toFixed(0)+' + '+parseFloat(charge)+'*)</span></br>';
					 }
						
					 if(discountedAmt >0){
					 show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>";
					 }	
		// Gst calculate	
			
			/* if($(value).find(".gst_val").val()>0 && $(value).find(".gst_type").val()==0){
				 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt inclusive : "+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()))+"</span>"} */
			 
			 if($(value).find(".gst_val").val() >0 && $(value).find(".gst_type").val()==1){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt exclusive : "+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()))+"</span>"}

					 
					 $(value).find(".show_pay").html(show_pay);
					 
				 }
				 else if($(value).find('#scheme_type').val() == 2)
					{
						var total_amt = accData.firstPayDisc!=1?amount:actamt;
						//var selweight=$(value).find('.sel_weight').val();
						var metal_rate = parseFloat($(value).find('.metal_rate').val());
						var charge=parseFloat($(value).find(".charge").val())*due_count;
						if(total_amt != '' && metal_rate != '')
						 {	
							var selweight = total_amt/metal_rate;
						 }
					 show_pay = '<b>'+accData.currency_symbol+' '+pay_amt+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+parseFloat((selweight*due_count)).toFixed(2)+' gm </span></br>';
					
						if(charge > 0){
							 show_pay = show_pay +'<span style="font-size: 11px;">('+(discountedAmt>0?(actamt*due_count)-parseFloat(discountedAmt):amount*due_count)+' + '+charge+'*)</span>';
						 }
						 
						 if(discountedAmt >0){
						 show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>"
						 }
			// Gst calculate
		
			if($(value).find(".gst_val").val()>0 && $(value).find(".gst_type").val()==0){
				 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt inclusive : "+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()))+"</span>"}
			 
			 if($(value).find(".gst_val").val() >0 && $(value).find(".gst_type").val()==1){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt exclusive : "+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()))+"</span>"}
			 
						$(value).find(".show_pay").html(show_pay);
				}
				else{
					var charge=parseFloat($(value).find(".charge").val())*due_count;
					//var amount=parseFloat($(value).find(".amount").val());
					show_pay = '<b>'+accData.currency_symbol+' '+pay_amt+'</b><br>';
						if(charge > 0){
								 show_pay = show_pay +'<span style="font-size: 11px;">('+(discountedAmt>0?(actamt*due_count)-parseFloat(discountedAmt):amount*due_count)+' + '+(charge)+'*)</span></br>';
							 }
							 
						 if(discountedAmt >0){
						 show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>"
						 
						 }
						 
		// Gst calculate
		
			if($(value).find(".gst_val").val()>0 && $(value).find(".gst_type").val()==0){
				 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt inclusive : "+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()))+"</span>"}
			 
			 if($(value).find(".gst_val").val() >0 && $(value).find(".gst_type").val()==1){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt exclusive : "+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()))+"</span>"}
			 
						 
				$(value).find(".show_pay").html(show_pay); 
					
				}
			}
		 }
	});
		$("#scheme-amount tbody tr").each(function(index, value) 
		{
							 
			if(!$(value).find(".select_chit").is(":checked"))
			 {
				$(value).find(".show_pay").empty();
				$(value).find(".ischecked").val(0);
				$(value).find(".payment_amt").val(0.00);
				$(value).find(".amount").val(0.00);
				$(value).find(".charge").val(0.00);
				$(value).find(".discount").val(0.00);
				$(value).find(".on_of_due").val(0);
				$(value).find(".actamt").val(0);
				$(value).find("#tot_sel_amt").val(0.00);
				/*alert($('#tot_amt').val());*/
			 } 
			else if($(value).find(".select_chit").is(":checked"))
				 {
					if(parseFloat($(value).find('.discount').val()) >0 ){
						//console.log(parseFloat(totamt));
						totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))-parseFloat($(value).find('.discount').val()))+($(value).find('.gst_type').val()==1?((parseFloat($(value).find('.sel_due').val()))*parseFloat($(value).find('.gst_val').val())):0));
					}else{
						//totamt = parseFloat(totamt) + ((parseFloat($(value).find('.payment_amt').val())*parseFloat($(value).find('.sel_due').val()))+($(value).find('.gst_type').val()==1?((parseFloat($(value).find('.sel_due').val()))*parseFloat($(value).find('.gst_val').val())):0));						
						totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0)));
					}
				 }
			 });
				//console.log(parseFloat($(value).find('.discount').val()));
		 $('#tot_amt').val(totamt);
		 $('#tot_sel_amt').html(totamt);
				
    });
    
    	
 $(document.body).submit(function(e){
		
		var i=0;
		 $("#scheme-amount tbody tr").each(function(index, value){
		 	
		 	if($(value).find(".select_chit").is(":checked"))
		    {	
		     i=i+1;
			}
		 })
		 if(i>0 && $('#tot_amt').val()>0){
			
		 	return true;
		 }
		 else{
			 
		 	  msg='<div class = "alert alert-warning"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please select chit(s) to proceed payment.</div>';
			        //stop the form from submitting
			         $('#error-msg').html(msg);
			       
		 	return false;
		 }
});
		
	
	//selected weights 
	
	$(document).on('change', '[type=radio][name=weight_gold]', function(ev)
	{
	  ev.preventDefault();
	   var selected_weight=0.000; 
	   var metal_rate = parseFloat($('#metal_rate').val()).toFixed(2); 
	 
		 $("input[name=weight_gold]:checked").each(function() {
		   selected_weight= parseFloat(parseFloat(selected_weight)+ parseFloat($(this).val())).toFixed(3);
		 });
			 
		$('#sel_wt').html(parseFloat(selected_weight).toFixed(3));
		 
 // calc amount for selected weight
		var tot_amt = Math.round(parseFloat(selected_weight) * parseFloat(metal_rate));
		 // console.log(tot_amt);
		$('#tot_amt').html(parseFloat(tot_amt).toFixed(2));
		$('#actAmt').val(parseFloat(tot_amt).toFixed(2));
		
	//calculate discount
	
	  var calc_discount = (accData.firstPayDisc_by==1?accData.discount:(parseFloat(tot_amt)*(parseFloat(accData.discount)/100)).toFixed(2));
	  
	if(accData.allowPayDisc==1)
	{
		var amount =(parseFloat(tot_amt) - parseFloat(calc_discount)).toFixed(2);
		var discountedAmt = parseFloat(calc_discount).toFixed(2);
	}
	else
	{
		var amount =parseFloat(tot_amt).toFixed(2);
		var discountedAmt = "";
	}
		
 // calculate gateway charge
	 var charge = 0.00;
	  if(accData.charge_type == 0 ){
	      var  charge = ((parseFloat(amount)*(parseFloat(accData.charge)/100)).toFixed(2));
	    }
		 else if(accData.charge_type == 1){
			var  charge = parseFloat(accData.charge);
		 }
	// Gst calculate	 
			/* accData.gst_type =1;
			  accData.gst =5;*/
			   var calc_gst = (((parseFloat(selected_weight) * parseFloat(metal_rate))*(parseFloat(accData.gst)/100)).toFixed(0));
			   
			if(accData.gst_type==0){
				var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));}
				
			if(accData.gst_type==1){	
				var payment_amt = Math.round((parseFloat(amount)+parseFloat(calc_gst))+parseFloat(charge));} 
		 
		 
	  //var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));
		
	  var  show_pay="<b>"+$('#currency_symbol').val()+' '+parseFloat(payment_amt)+'</b><button type="button" value="'+parseFloat($('#id_scheme_account').val())+'"  class="btn btn-small btn-warning chg_wgt pull-right">'+parseFloat(selected_weight).toFixed(1)+' g Change</button><br/>';		
			
		 if(charge > 0){
			 show_pay = show_pay +'<span style="font-size: 11px;">('+parseFloat(amount)+' + '+parseFloat(charge)+'*)</span></br>';
		 }
		
		 if(discountedAmt >0){
		 show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>"
		 }
		 // Gst calculate	
			/* if(calc_gst >0 && accData.gst_type==0){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt inclusive : "+parseFloat(calc_gst)+"</span>"} */
			 
			 if(calc_gst >0 && accData.gst_type==1){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt exclusive : "+parseFloat(calc_gst)+"</span>"}
		 
	$("#scheme-amount tbody tr").each(function(index, value) 
	{
		var id =  $(value).find('.id_scheme_account').val();
		if($(value).find("#select_id_"+id).is(":checked") && id == parseFloat($('#id_scheme_account').val()))
		{
			 $(value).find(".show_pay").html(show_pay); 
			 $(value).find(".payment_amt").val(payment_amt);
			 $(value).find(".amount").val(amount);
			 $(value).find(".gst_val").val(calc_gst);
			 $(value).find(".gst_type").val(accData.gst_type);
			 $(value).find(".charge").val(charge);
			 $(value).find(".discount").val(discountedAmt);
			 $(value).find(".sel_weight").val(selected_weight);
			 $(value).find(".payable").val(Math.round(parseFloat(selected_weight) * parseFloat(metal_rate)));
			 $(value).find(".actamt").val(Math.round(parseFloat(selected_weight) * parseFloat(metal_rate)));
			 $(value).find(".metal_rate").val(metal_rate);
			 $(value).find(".ischecked").val(1);
				
			 //calculate total selected payment amount
			  var totamt = 0.00;
			 
			 $("#scheme-amount tbody tr").each(function(index, value) 
			 {
				 if(parseFloat($(value).find('.discount').val())>0){
					 
				 totamt =(parseFloat(totamt)+((((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))-parseFloat($(value).find('.discount').val()))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0)));
			}else{
				//totamt = parseFloat(totamt) + ((parseFloat($(value).find('.payment_amt').val())*parseFloat($(value).find('.sel_due').val()))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0));
				totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0)));
				
				
				
			}
				 
			 })
			 
			 $('#tot_amt').val(totamt);
			 $('#tot_sel_amt').html(totamt);
		 }
	  });
				
   });
   
});

payment = {
	setcontent:function(accData){
		
		
		
			    var chit = accData.chit;
	  			var weight = accData.weights;
			    var rate  = accData.metal_rates;
			    var customer = accData.customer;
			    var eligible_weight= parseFloat(chit.max_weight).toFixed(3) - parseFloat(chit.current_total_weight).toFixed(3);
			    
			    var content ="<div class='pay-content'><h3>"+(chit.chit_number!=''?chit.chit_number:chit.scheme_name)+"</h3><p>Select weight to make payment<p><div id='error-msg'></div>  <input type='hidden' id='allowPayDisc'  value='"+(chit.allowPayDisc)+"'/><input type='hidden' id='firstPayDisc_by'  value='"+(chit.firstPayDisc_by)+"'/><input type='hidden' id='discount'  value='"+(chit.discount)+"'/><input type='hidden' id='charge'  value='"+(chit.charge_type)+"'/><input type='hidden' id='charge'  value='"+(chit.charge)+"'/><input type='hidden' id='id_scheme_account'  value='"+(chit.id_scheme_account)+"'/><input type='hidden' id='currency_symbol'  value='"+(chit.currency_symbol)+"'/></div>"
			    
				var pay = "<input type='hidden' id='metal_rate' name='pay[udf3]' value='"+(chit.scheme_type==1?parseFloat(rate.goldrate_22ct).toFixed(2):'')+"'/>"
						 // +"<h4>Payment Amount : Rs. <span id='tot_amt'>"+(chit.scheme_type==0?parseFloat(chit.payable).toFixed(2):'0.00')+"</span> </h4>";
			 	
			 	
			    var weight_check='<div class="rate-table"><table class="table table-bordered table-striped table-responsive text-center">'+
			       '<tr><th colspan="3" style="text-align:center" ><h3 > Gold 22k 1gm rate : '+parseFloat(rate.goldrate_22ct).toFixed(2)+'</h3></th></tr>'+
			        '<tr><td><div style="float:left">Eligible:</div><div style="float:right">'+parseFloat(eligible_weight).toFixed(3)+' g</div></td><td><div style="float:left">Selected:</div><div style="float:right"><span id="sel_wt" >0.000</span> g</div></td></tr>'+ 
			                           '<tr><th>Weight</th><th>Amount</th></tr>';
							   
				$.each(weight, function() {
					
					 if(( parseFloat(chit.current_total_weight) + parseFloat(this.weight)) <= parseFloat(chit.max_weight))
					 {
					 		  weight_check +="<tr><td><input type='radio' name='weight_gold' value='"+this.weight+"' />	"+parseFloat(this.weight).toFixed(3)+" gram </td><td>"+chit.currency_symbol+' '+parseFloat(this.weight*rate.goldrate_22ct).toFixed(2)+" </td></tr>";
					 } 
				
				});	   
				weight_check +='<table></div>';
	
				$('#payModal .modal-body .pay-content').remove();
				$('#payModal .modal-body').append(content);
				$('#payModal .modal-body .pay-content').append(weight_check);
				$('#payModal .modal-body .pay-content').append(pay);
                $('#payModal').modal('show', {backdrop: 'static'});
				
			
	},
	changeWgt:function(id_sch_acc){
			$('.overlay').css('display','block');
			 //get chit scheme content
		  $.ajax({
			type: "GET",
			url: baseURL+"index.php/paymt/getPaymentContent/"+id_sch_acc+"?nocache=" + my_Date.getUTCSeconds(),
			dataType: "json",
			cache: false,
			success: function(data) {
					$('.overlay').css('display','none');
			  
			    accData = data.chit;
					 if(accData.scheme_type==1){
						 var paycontent = payment.setcontent(data);
						 if(paycontent){
							$('#payModal .modal-body').html(paycontent);
							$('#payModal').modal('show', {backdrop: 'static'});
						 }
					}
				}
		     });//end of ajaxcall
			
		}
	}
