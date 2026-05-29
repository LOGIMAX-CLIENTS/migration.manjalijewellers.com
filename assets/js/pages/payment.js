$(document).ready(function() {
	
//due count	
	var accData = [];
	
	// define 0 init
	
	//	$(".pay_amtwgt").val(0);
	
	// define 0 init
	 
	$(".redeem_request").keyup(function(){
	    if((parseFloat($(".wallet").val()) < parseFloat($(".redeem_request").val()) || parseFloat($(".redeem_request").val()) <0)){
	    	$(".redeem_request").val($(".wallet").val()); 
		}
		var tot = parseFloat($("#tot_amt").val())-parseFloat($(".redeem_request").val());
		$('#tot_sel_amt').html(tot);
	});
 
    /*$("#proceed_pay").on("click",function(ev){
    	ev.preventDefault();
    	if($("input[name='payment[id_pg]']:checked").val() == 0){
    		alert("Choose Gateway to proceed");
    		return false;
		}else{
			$("#proceed_pay").submit();
		}
    })*/
    
    $(".brn_btn").on("click",function(ev){
    	$(".brn_btn").removeClass("theme-btn-bg");
    	$(this).addClass("theme-btn-bg");
    	$(".brn_gateways").css("display","none");
    	$("#brn_"+this.value).css("display","block");
    	$(".pay_row").css("display","none");
    	$(".brn_row_"+this.value).css("display","revert");
    })
    
    $(".ischk_wallet_pay").on("click",function(ev){
		 // Set total amount and wallet amount 
		 var totamt = $('#tot_amt').val();
		 var can_redeem = 0; 
		 if($(".ischk_wallet_pay").is(":checked") && parseFloat($(".wallet_balance").val()) > 0){
			 var allowed_redeem =  ($('.redeem_percent').val() > 0 ? (totamt*(parseFloat($('.redeem_percent').val())/100)) : 0);
			 wallet_balance = parseFloat($('.wallet_balance').val());
			 if( allowed_redeem > wallet_balance ){
			 	can_redeem = wallet_balance;
			 }else{
			 	can_redeem = allowed_redeem;
			 }
		 }
		 $('.wallet').val(can_redeem);$('.redeem_request').val(can_redeem);
		 $('#tot_amt').val(totamt); 
		 $('#tot_sel_amt').html(totamt-can_redeem);
	})	
	
	// on selecting chit scheme
    $("input[name=sch_all]").on("click",function(ev)
	{
        var payment_amount=0;
        var id=$(this).parent().parent().find(".id_scheme_account").val();
        $("#scheme-amount tbody tr").each(function(index, value) 
        {
		 if($(value).find("#select_id_"+id).is(":checked"))
		 {
            var pid =parseFloat(id);
            my_Date = new Date();
            $('.overlayy').css('display','block');
		   //get chit scheme content
		    $.ajax({
    			type: "GET",
    			url: baseURL+"index.php/paymt/getPaymentContent/"+pid+"?nocache=" + my_Date.getUTCSeconds(),
    			dataType: "json",
    			cache: false,
    			success: function(data) { 
    			    
    			    console.log(data);
    			    
    				$('.overlayy').css('display','none');
    		        accData = data.chit;
    		        var rate  = data.metal_rates;					

		            // wallet payment 
		 
        			$('.wallet_balance').val(parseFloat(data.walletbalance.wal_balance));
        			$('.redeem_percent').val(parseFloat(data.walletbalance.redeem_percent));
        			$('.gst').val(parseFloat(accData.gst));
        			if($('.wallet_balance').val()!='0'){ 
        				$(".eligible_walletamt").css("display","block"); 
        			}else{ 				
        				$(".eligible_walletamt").css("display","none"); 
        			}
        			$('.wallet').val(parseFloat(data.walletbalance.wal_balance)); 
        			
        			
		            if(accData.scheme_type==0)
            		{		
            			 var due_no = parseInt(accData.paid_installments) + 1;
            			 var calc_discount = (accData.allowPayDisc==1?(accData.discount_type==0 ?accData.discount :(accData.discount_installment==due_no ? accData.discount:0.00)):0.00);
            			   
            			 if(accData.discount_set==1&&accData.allowPayDisc==1&& calc_discount>0){
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
            			var schPayData = {
							'instalment_amt'	: accData.payable,
							'sel_dues' 			: 1,
							'discount' 			: discountedAmt,
							'gst_type' 			: accData.gst_type,
							'gst_percent' 		: accData.gst,
							//'gold_metal_rate'	: rate.goldrate_22ct,
							'metal_rate'        : rate[accData.rate_field],
							'is_fixed_weight'   : (parseFloat(accData.scheme_type) == 1 && parseFloat(accData.min_weight) == parseFloat(accData.max_weight)) ? 1 : 0 ,
							'max_weight'        : parseFloat(accData.max_weight),
			            	'scheme_type'		: accData.scheme_type,
			            	'flexible_sch_type'	: accData.flexible_sch_type
						}
						var calcData = calculate_payAmt(schPayData);
						var calc_gst = calcData.gst_amt; // Per installment gst amount
            			//var calc_gst = ((parseFloat(accData.payable)*(parseFloat(accData.gst)/100)).toFixed(0));
            			   
            			if(accData.gst_type==0){
            				var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));
            			}            				
            			if(accData.gst_type==1){	
            				var payment_amt = Math.round((parseFloat(amount)+parseFloat(calc_gst))+parseFloat(charge));
            			}
            			 $(value).find(".payment_amt").val(payment_amt);
            			 $(value).find(".amount").val(amount);
            			 $(value).find(".gst_val").val(calc_gst);
            			 $(value).find(".gst_type").val(accData.gst_type);
            			 $(value).find(".charge").val(charge);
            			 $(value).find(".discount").val(discountedAmt);
            			 $(value).find(".metal_rate").val(rate[accData.rate_field]);
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
            			 
            			 // Set total amount and wallet amount 
            			 var can_redeem = 0;
            			 if($('.ischk_wallet_pay').val() == 1 && parseFloat($(".wallet_balance").val()) > 0){
            				 var allowed_redeem =  ($('.redeem_percent').val() > 0 ? (totamt*(parseFloat($('.redeem_percent').val())/100)) : 0);
            				 wallet_balance = parseFloat($('.wallet_balance').val());
            				 if( allowed_redeem > wallet_balance ){
            				 	can_redeem = wallet_balance;
            				 }else{
            				 	can_redeem = allowed_redeem;
            				 }
            			 }
            			 $('.wallet').val(can_redeem);$('.redeem_request').val(can_redeem);
            			 $('#tot_amt').val(totamt); 
            			 $('#tot_sel_amt').html(totamt-can_redeem);		 
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
            		 var calc_discount =(accData.allowPayDisc==1?(accData.discount_type==0 ?accData.discount :(accData.discount_installment==due_no ? accData.discount:0.00)):0.00);
             
            	    // check discount settings to calculate discount
            		 if(accData.discount_set==1&&accData.allowPayDisc==1){
            
            			var amount =(parseFloat(accData.max_weight*rate[accData.rate_field]) - parseFloat(calc_discount)).toFixed(2);
            			var discountedAmt = parseFloat(calc_discount).toFixed(2);
            		 }
            		 else{
            			
            			var amount = parseFloat(accData.max_weight*rate[accData.rate_field]);
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
                    var schPayData = {
						'instalment_amt'	: accData.min_amount,
						'sel_dues' 			: 1,
						'discount' 			: discountedAmt,
						'gst_type' 			: accData.gst_type,
						'gst_percent' 		: accData.gst,
						'is_fixed_weight'   : (parseFloat(accData.scheme_type) == 1 && parseFloat(accData.min_weight) == parseFloat(accData.max_weight)) ? 1 : 0 ,
						'max_weight'        : parseFloat(accData.max_weight),
						//'gold_metal_rate'	: rate.goldrate_22ct,
						'metal_rate'        : rate[accData.rate_field],
		            	'scheme_type'		: accData.scheme_type,
		            	'flexible_sch_type'	: accData.flexible_sch_type
					}
					var calcData = calculate_payAmt(schPayData);
					var calc_gst = calcData.gst_amt; // Per installment gst amount 
            		//var calc_gst = ((parseFloat(accData.max_weight*rate.goldrate_22ct)*(parseFloat(accData.gst)/100)).toFixed(0));
            			   
            			if(accData.gst_type==0){
            				var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));}
            				
            			if(accData.gst_type==1){	
            		
            				var payment_amt = Math.round((parseFloat(amount)+parseFloat(calc_gst))+parseFloat(charge));}
            		 
            		// var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));				
            	
            		var show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(payment_amt)+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+parseFloat(accData.max_weight).toFixed(3)+' gm </span></br>';
            		
            		 if(charge > 0){
            			 show_pay = show_pay +'<span style="font-size: 11px;">('+parseFloat(amount).toFixed(0)+' + '+parseFloat(charge)+'*)</span></br>';
            		 }
            		
            		 if(discountedAmt >0){
            		 show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>";
            		 }
            		 
            		// Gst calculate	
            		if(calc_gst >0 && accData.gst_type==1){
            			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt exclusive : "+parseFloat(calc_gst)+"</span>";
            		} 
            						 
            		 $(value).find(".show_pay").html(show_pay);	
            		 $(value).find(".payment_amt").val(payment_amt);
            		 $(value).find(".amount").val(amount);
            		 $(value).find(".gst_val").val(calc_gst);
            		 $(value).find(".gst_type").val(accData.gst_type);
            		 $(value).find(".ischecked").val(1);
            		 $(value).find(".sel_weight").val(accData.max_weight);
            		 $(value).find(".metal_rate").val(rate[accData.rate_field]);
            		 $(value).find(".no_of_due").val(due_no);
            		 $(value).find(".sel_due").val(1);
            		 $(value).find(".discount").val(discountedAmt);
            		 $(value).find(".allowed_dues").val(accData.allowed_dues);
            		 $(value).find(".charge").val(charge);
            		 $(value).find(".payable").val(accData.payable);
            		 $(value).find(".actamt").val(parseFloat(accData.max_weight*rate[accData.rate_field]));
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
            		
            		 // Set total amount and wallet amount 
            		 var can_redeem = 0;
            		 if($('.ischk_wallet_pay').val() == 1 && parseFloat($(".wallet_balance").val()) > 0){
            			 var allowed_redeem =  ($('.redeem_percent').val() > 0 ? (totamt*(parseFloat($('.redeem_percent').val())/100)) : 0);
            			 wallet_balance = parseFloat($('.wallet_balance').val());
            			 if( allowed_redeem > wallet_balance ){
            			 	can_redeem = wallet_balance;
            			 }else{
            			 	can_redeem = allowed_redeem;
            			 }
            		 }
            		 $('.wallet').val(can_redeem);$('.redeem_request').val(can_redeem);
            		 $('#tot_amt').val(totamt); 
            		 $('#tot_sel_amt').html(totamt-can_redeem);
            		 
               }
	           else if(accData.scheme_type==2)
               {
            		var metal_rate  = data.metal_rates[accData.rate_field];		
            	
            		if(metal_rate != ''){
            			var selweight = accData.payable/metal_rate;
            		}
            		else{
            			var selweight = 0;
            		}
            		var due_no = parseInt(accData.current_paid_installments) + 1;
            					
            	    var calc_discount = (accData.allowPayDisc==1?(accData.discount_type==0 ?accData.discount :(accData.discount_installment==due_no ? accData.discount:0.00)):0.00);
            			   
                    // check discount settings to calculate discount
            					
        			 if(accData.discount_set==1&&accData.allowPayDisc==1&& calc_discount>0){
        				var amount =(parseFloat(accData.payable) - parseFloat(calc_discount)).toFixed(0);
        				var discountedAmt = parseFloat(calc_discount).toFixed(0);
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
        			 var schPayData = {
						'instalment_amt'	: accData.payable,
						'sel_dues' 			: 1,
						'discount' 			: discountedAmt,
						'gst_type' 			: accData.gst_type,
						'gst_percent' 		: accData.gst,
						'is_fixed_weight'   : (parseFloat(accData.scheme_type) == 1 && parseFloat(accData.min_weight) == parseFloat(accData.max_weight)) ? 1 : 0 ,
						'max_weight'        : parseFloat(accData.max_weight),
						//'gold_metal_rate'	: rate.goldrate_22ct,
						'metal_rate'        : rate[accData.rate_field],
		            	'scheme_type'		: accData.scheme_type,
		            	'flexible_sch_type'	: accData.flexible_sch_type
					}
						console.log(metal_rate);
					var calcData = calculate_payAmt(schPayData);
					var calc_gst = calcData.gst_amt; // Per installment gst amount 
        			// var calc_gst = ((parseFloat(accData.payable)*(parseFloat(accData.gst)/100)).toFixed(0));
        			   
        			 if(accData.gst_type==0){
        				var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));
        			 }
            				
        			 if(accData.gst_type==1){	
        				var payment_amt = Math.round((parseFloat(amount)+parseFloat(calc_gst))+parseFloat(charge));
        			 }
        		     var show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(payment_amt)+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+calcData.metal_weight+' gm </span></br>';
        			 if(charge > 0){
        				 show_pay = show_pay +'<span style="font-size: 11px;">('+parseFloat(amount)+' + '+parseFloat(charge)+'*)</span></br>';
        			 }
            			
            		 if(discountedAmt >0){
            			 show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>";
            		 }
                    //Gst calculate
            		if(calc_gst >0 && accData.gst_type==0){
            			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt inclusive : "+parseFloat(calc_gst)+"</span>";
            		}
            			 
            		if(calc_gst >0 && accData.gst_type==1){
            		    show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt exclusive : "+parseFloat(calc_gst)+"</span>";
            		}
            			 
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
            		 // Set total amount and wallet amount 
            		 var can_redeem = 0;
            		 if($('.ischk_wallet_pay').val() == 1 && parseFloat($(".wallet_balance").val()) > 0){
            			 var allowed_redeem =  ($('.redeem_percent').val() > 0 ? (totamt*(parseFloat($('.redeem_percent').val())/100)) : 0);
            			 wallet_balance = parseFloat($('.wallet_balance').val());
            			 if( allowed_redeem > wallet_balance ){
            			 	can_redeem = wallet_balance;
            			 }else{
            			 	can_redeem = allowed_redeem;
            			 }
            		 }
            		 $('.wallet').val(can_redeem);$('.redeem_request').val(can_redeem);
            		 $('#tot_amt').val(totamt); 
            		 $('#tot_sel_amt').html(totamt-can_redeem);
            	}
	            else if(accData.scheme_type==3)
		        {
        			//disabled buttons
        			//if(accData.get_amt_in_schjoin==1 && accData.paid_installments==0)
        			if(accData.firstPayamt_as_payamt==1 && accData.get_amt_in_schjoin==1)
        			{
        				$(value).find(".pay_amtwgt").attr('disabled', true);	
        			}
        			else
        			{
        				$(value).find(".pay_amtwgt").attr('disabled', false);
        			} 
        			$(value).find(".cal_amt").attr('disabled', false); 
        			//disabled buttons
        			  
        
        			$(value).find(".pay_amtwgt").val(accData.min_amount);
        			var metal_rate  = data.metal_rates[accData.rate_field];
        			var total_paid_amount=$(value).find(".total_paid_amount").val();
        			var last_paid_date=$(value).find(".last_paid_date").val();
        			$(value).find(".gst_type").val(accData.gst_type);
        			$(value).find(".metal_rate").val(metal_rate);
        			$(value).find(".charge").val(charge);			
        			$(value).find(".ischecked").val(1);
        			$(value).find(".sel_due").val(1);
        			$(value).find(".allowed_dues").val(accData.allowed_dues);
        			$(value).find(".total_paid_amount").val(total_paid_amount);
        			$(value).find(".last_paid_date").val(last_paid_date);

                    if(($(value).find(".pay_amtwgt").val()!='' && accData.firstPayamt_as_payamt==1) || (accData.get_amt_in_schjoin ==1 && $(value).find(".pay_amtwgt").val()!=''))
                    {
                        var firstPayamt_as_payamt=true;                 
                        var payable = parseFloat($(value).find(".pay_amtwgt").val());
                        var allowed_dues = parseFloat($(value).find(".allowed_dues").val()); 
                        var payment_limit = accData.max_amount!=0 && (accData.max_weight==0 || accData.max_weight!='')?(accData.firstPayamt_as_payamt==1 && accData.paid_installments>0? payable:parseFloat(accData.current_total_amount) + parseFloat(payable)):parseFloat(accData.current_total_weight) + parseFloat(payable/$(value).find(".metal_rate").val()); //Checking Firstpay amount as payment amount(firstPayamt_as_payamt)
                        //var maxamount= accData.max_amount!=0 && (accData.max_weight==0 || accData.max_weight!='')? accData.max_amount:accData.max_weight;
                      //  if((accData.firstPayamt_maxpayable==1 || accData.firstPayamt_as_payamt==1)&&accData.flexible_sch_type==2 && accData.flx_denomintion != null) //For Denomination checking
                        if((accData.firstPayamt_maxpayable==1 || accData.firstPayamt_as_payamt==1)&& accData.flexible_sch_type<=3 && accData.flx_denomintion > 0) //For Denomination checking
                        {	
                            if(($(value).find(".pay_amtwgt").val())%(accData.flx_denomintion)!=0)
                            {
                                firstPayamt_as_payamt=false;
                            }
                            else
                            {
                                firstPayamt_as_payamt=true;
                                $('#error-msg').html('');
                            }
                        }
                        if(firstPayamt_as_payamt==true)
                        {
                            var allowed_dues = parseFloat($(value).find(".allowed_dues").val()); 
                            if((accData.max_amount >= payment_limit)&& ( (accData.current_total_weight <= accData.max_amount)||(accData.current_total_amount <= accData.max_amount))&& ((accData.max_chance > accData.current_chances_pay) || (accData.allowed_dues>=allowed_dues)))
                            {								
                                if((accData.min_amount <= payable))
                            	{ 
                                    var metal_rate  = $(value).find(".metal_rate").val();
                                    if(metal_rate!=='' && accData.wgt_convert==0 &&(accData.flexible_sch_type==2 || accData.flexible_sch_type==3))
                                    {
                                    	var selweight = payable/metal_rate;
                                    }
                                    else{
                                    	var selweight = '-';
                                    }
                                    var due_no = parseInt(accData.current_paid_installments) + 1;
                                    //calculate discount
                                    var calc_discount = (accData.firstPayDisc_by==1?accData.discount:(parseFloat(payable)*(parseFloat(accData.discount)/100)).toFixed(2));
                                    // check discount settings to calculate discount
                                    if(accData.allowPayDisc==1){
	                                    var amount =(parseFloat(payable) - parseFloat(calc_discount)).toFixed(2);
	                                    var discountedAmt = parseFloat(calc_discount).toFixed(2);
                                    }
                                    else{
	                                    var amount = parseFloat(payable);
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
                            		var schPayData = {
										'instalment_amt'	: payable,
										'sel_dues' 			: 1,
										'discount' 			: discountedAmt,
										'gst_type' 			: accData.gst_type,
										'gst_percent' 		: accData.gst,
										'is_fixed_weight'   : (parseFloat(accData.scheme_type) == 1 && parseFloat(accData.min_weight) == parseFloat(accData.max_weight)) ? 1 : 0 ,
										'max_weight'        : parseFloat(accData.max_weight),
										//'gold_metal_rate'	: $(value).find(".metal_rate").val(),
							            'metal_rate'        : rate[accData.rate_field],
						            	'scheme_type'		: accData.scheme_type,
						            	'flexible_sch_type'	: accData.flexible_sch_type
									}
									var calcData = calculate_payAmt(schPayData);
									var calc_gst = calcData.gst_amt; // Per installment gst amount  
                                    //var calc_gst = ((parseFloat(payable)*(parseFloat(accData.gst)/100)).toFixed(0));
                                    if(accData.gst_type==0)
                                    {
                                    var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));
                                    }
                                    if(accData.gst_type==1)
                                    {	
                                    var payment_amt = Math.round((parseFloat(amount)+parseFloat(calc_gst))+parseFloat(charge));
                                    }
                                    if(accData.wgt_convert==0 && (accData.flexible_sch_type==2 || accData.flexible_sch_type==3)){
                                    var show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(payment_amt)+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+calcData.metal_weight+' gm </span></br>'; 
                                    }
                                    else{
                                    var show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(payment_amt);
                                    }
                                    if(charge > 0){
                                    show_pay = show_pay +'<span style="font-size: 11px;">('+parseFloat(amount)+' + '+parseFloat(charge)+'*)</span></br>';
                                    }
                                    if(discountedAmt >0){
                                    show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>";
                                    }
                            //Gst calculate
                                    if(calc_gst >0 && accData.gst_type==0){
                                    show_pay =	show_pay +  "</br><span style='font-size: 11px;'> GST amt inclusive : "+parseFloat(calc_gst)+"</span>"}
                                    
                                    if(calc_gst >0 && accData.gst_type==1){
                                    show_pay =	show_pay +  "</br><span style='font-size: 11px;'> GST amt exclusive : "+parseFloat(calc_gst)+"</span>"}
                                    
                                    $(value).find(".show_pay").html(show_pay)	
                                    $(value).find(".payment_amt").val(payment_amt);
                                    $(value).find(".amount").val(amount);
                                    $(value).find(".gst_val").val(calc_gst);
                                    $(value).find(".gst_type").val(accData.gst_type);
                                    $(value).find(".sel_weight").val(calcData.metal_weight);
                                    $(value).find(".metal_rate").val(metal_rate);
                                    $(value).find(".charge").val(charge);
                                    $(value).find(".discount").val(discountedAmt);
                                    $(value).find(".ischecked").val(1);
                                    $(value).find(".no_of_due").val(due_no);
                                    $(value).find(".sel_due").val(1);
                                    $(value).find(".allowed_dues").val(accData.allowed_dues);
                                    $(value).find(".actamt").val(payable);
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
                                     var can_redeem = 0;
                                    if($('.ischk_wallet_pay').val() == 1 && parseFloat($(".wallet_balance").val()) > 0)
                                    {
                                            var allowed_redeem =  ($('.redeem_percent').val() > 0 ? (totamt*(parseFloat($('.redeem_percent').val())/100)) : 0);
                                            wallet_balance = parseFloat($('.wallet_balance').val());
                                            if( allowed_redeem > wallet_balance ){
                                            can_redeem = wallet_balance;
                                            }else{
                                            can_redeem = allowed_redeem;
                                            }
                                    }
                                    $('.wallet').val(can_redeem);$('.redeem_request').val(can_redeem);
                                    $('#tot_amt').val(totamt); 
                                    $('#tot_sel_amt').html(totamt-can_redeem); 
                                }
                                else
                                {
                                    $(value).find(".show_pay").html('');
                                    $(value).find(".actamt").val(0);
                                    $(value).find(".sel_due").val(0);				
                                    $(value).find(".charge").val(0);				
                                    $(value).find(".discount").val(0);				
                                    $(value).find(".gst_type").val(0);				
                                    $(value).find(".gst_val").val(0);
                                    $(value).find(".sel_due").val(1);
                                    $(value).find(".pay_amtwgt").val(accData.min_amount);
                                    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong> Warning! </strong> You could not pay less than <strong> Rs  '+accData.min_amount+' </strong></div>';
                                    $('#error-msg').html(msg);
                                }
                            }
                            else 
                            { 
                                $(value).find(".show_pay").html('');
                                $(value).find(".actamt").val(0);
                                $(value).find(".sel_due").val(0);				
                                $(value).find(".charge").val(0);				
                                $(value).find(".discount").val(0);				
                                $(value).find(".gst_type").val(0);				
                                $(value).find(".gst_val").val(0);
                                $(value).find(".sel_due").val(1);
                                $(value).find(".pay_amtwgt").val(accData.min_amount);
                                var Eligible_payment1 = (accData.max_amount!=0 && (accData.max_weight==0 || accData.max_weight!='')? (((accData.firstPayamt_maxpayable==1 || accData.firstPayamt_as_payamt==1)&&(accData.paid_installments>0) &&(accData.flexible_sch_type==1 || accData.flexible_sch_type==2)) ? parseFloat(accData.firstPayment_amt): (parseFloat(accData.max_amount))):parseFloat(accData.max_weight - accData.current_total_weight));
                                var  Eligible_pay = (accData.max_amount!=0 && (accData.max_weight==0 || accData.max_weight!='')? Eligible_payment1:(parseFloat((parseFloat(accData.max_weight) - parseFloat(accData.current_total_weight))*$(value).find(".metal_rate").val()).toFixed(2)));
                                msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong> Warning! </strong> You could not pay more than <strong> Rs '+Eligible_pay+' </strong></div>';
                                //stop the form from submitting
                                $('#error-msg').html(msg);		  			
                            }
                        }
                        else
                        {
    	                    $(value).find(".show_pay").html('');
    	                    $(value).find(".actamt").val(0);
    	                    $(value).find(".sel_due").val(0);				
    	                    $(value).find(".charge").val(0);				
    	                    $(value).find(".discount").val(0);				
    	                    $(value).find(".gst_type").val(0);				
    	                    $(value).find(".gst_val").val(0);
    	                    $(value).find(".sel_due").val(1);
    	                    $(value).find(".pay_amtwgt").val(accData.min_amount);
    	                    msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong> Warning! </strong>Please Enter Amount in multiples of '+(accData.flx_denomintion)+' </strong></div>';
    	                    //stop the form from submitting
    	                    $('#error-msg').html(msg);
                        }
                    }
                   
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
			//$(value).find(".pay_amtwgt").val(0);
			$(value).find(".pay_amtwgt").attr('disabled', true);
			//$(value).find(".pay_amtwgt").val(0);
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
		 // Set total amount and wallet amount 
		 var can_redeem = 0;
		 if($('.ischk_wallet_pay').val() == 1 && parseFloat($(".wallet_balance").val()) > 0){
			 var allowed_redeem =  ($('.redeem_percent').val() > 0 ? (totamt*(parseFloat($('.redeem_percent').val())/100)) : 0);
			 wallet_balance = parseFloat($('.wallet_balance').val());
			 if( allowed_redeem > wallet_balance ){
			 	can_redeem = wallet_balance;
			 }else{
			 	can_redeem = allowed_redeem;
			 }
		 }
		 $('.wallet').val(can_redeem);$('.redeem_request').val(can_redeem);
		 $('#tot_amt').val(totamt); 
		 $('#tot_sel_amt').html(totamt-can_redeem);
	}); 
 });
	
	$(document.body).on('click', '.chg_wgt' ,function(){
		payment.changeWgt($(this).val());
	});
		
 $(document).on('click', ".dec_due,.incre_due", function(e){
	var due_val = $(this).val();
	var due_class = $(this).attr("class");
	 var totamt = 0.00;
	    $("#scheme-amount tbody tr").each(function(index, value)
		{  
	    	if((index+1) == due_val)
			 {	 
				var sel_due = parseFloat($(value).find(".sel_due").val()) ;  
				var allowed_dues = parseFloat($(value).find(".allowed_dues").val()); 
				var proceed = ( due_class == "incre_due" ? (sel_due < allowed_dues) : (sel_due > 1) );
				if(proceed)
				 {
					var due_count = ( due_class == "incre_due" ? (sel_due+1) : (sel_due-1) );
					var discountedAmt = parseFloat($(value).find(".discount").val());
					var calc_discount = (accData.allowPayDisc==1?(accData.discount_type==0 ?accData.discount*due_count :((accData.discount_installment==due_count) ? accData.discount:0.00)):0.00);
					if(accData.discount_set==1&&accData.allowPayDisc==1&& calc_discount>0){
					var amount =(parseFloat(accData.payable) - parseFloat(calc_discount)).toFixed(0);
                   
                    var discountedAmt = parseFloat(calc_discount).toFixed(0);
                     $(value).find(".discount").val(discountedAmt);
                    }
                    else{
                    var amount = parseFloat(accData.payable);
                     $(value).find(".discount").val(0);
                     discountedAmt=0;
                     
			        } 
			        
					$(value).find(".sel_due").val(due_count);
					var charge = parseFloat($(value).find(".charge").val());
					var actamt = parseFloat($(value).find(".actamt").val());
					var amount = parseFloat($(value).find(".amount").val());
					var pay_amt = ((amount*due_count) + (charge*due_count));
					console.log(pay_amt);
					// Gst calculate
			  
					if($(value).find(".gst").val()> 0 && pay_amt>0){
						//var pay_amt = parseFloat(pay_amt)+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()));
						var schPayData = {
							'instalment_amt'	: amount,
							'sel_dues' 			: due_count,
							'discount' 			: discountedAmt,
							'gst_type' 			: parseFloat($(value).find(".gst_type").val()),
							'gst_percent' 		: parseFloat($(value).find(".gst").val()),
							'is_fixed_weight'   : parseFloat($(value).find("#is_flexible_wgt").val()) ,
							'max_weight'        : parseFloat($(value).find("#max_weight").val()) ,
							//'gold_metal_rate'	: parseFloat($(value).find(".metal_rate").val()),
							'metal_rate'        : parseFloat($(value).find(".metal_rate").val()),
			            	'scheme_type'		: parseFloat($(value).find("#scheme_type").val()),
			            	'flexible_sch_type'	: parseFloat($(value).find("#flexible_sch_type").val())
						}
						var calcData = calculate_payAmt(schPayData);
						var pay_amt = calcData.payment_amt; // Per installment payment amount
					}
					
				 	//added				
					if($(value).find('#scheme_type').val() == 1 && $(value).find('#is_flexible_wgt').val() == 0)
					{
						var selweight=$(value).find(".sel_weight").val();
						var charge=parseFloat($(value).find(".charge").val())*due_count;
						
							 show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(pay_amt).toFixed(0)+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+calcData.metal_weight+' gm </span></br>';
					
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
				
					show_pay = '<b>'+accData.currency_symbol+' '+pay_amt+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+parseFloat((selweight*due_count)).toFixed(3)+' gm </span></br>';
					
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
			 else if($(value).find('#scheme_type').val() == 3)
			 {
				var amount = parseFloat($(value).find(".pay_amtwgt").val())*due_count;
				var metal_rate  = $(value).find(".metal_rate").val();
				if(metal_rate!=='' && accData.wgt_convert==0 &&(accData.flexible_sch_type==2 || accData.flexible_sch_type==3))
				{
					var selweight = amount/metal_rate;
				}
				else{
					var selweight = '-';
				}

				if(accData.wgt_convert==0 && (accData.flexible_sch_type==2 || accData.flexible_sch_type==3))
				{
				  	var show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(amount)+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+calcData.metal_weight+' gm </span></br>'; 
				}
				else
				{
				  	show_pay = '<b>'+accData.currency_symbol+' '+amount+'</b>';
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
			
		 } 
		else if($(value).find(".select_chit").is(":checked"))
		 {
			if(parseFloat($(value).find('.discount').val()) >0 ){
			
		   		totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))-parseFloat($(value).find('.discount').val())+($(value).find('.gst_type').val()==1?((parseFloat($(value).find('.sel_due').val()))*parseFloat($(value).find('.gst_val').val())):0)));
		    	
			}else{
				totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0)));
			}
		 }
	 });
		// Set total amount and wallet amount 
		 var can_redeem = 0;
		 if($('.ischk_wallet_pay').val() == 1 && parseFloat($(".wallet_balance").val()) > 0){
			 var allowed_redeem =  ($('.redeem_percent').val() > 0 ? (totamt*(parseFloat($('.redeem_percent').val())/100)) : 0);
			 wallet_balance = parseFloat($('.wallet_balance').val());
			 if( allowed_redeem > wallet_balance ){
			 	can_redeem = wallet_balance;
			 }else{
			 	can_redeem = allowed_redeem;
			 }
		 }
		 $('.wallet').val(can_redeem);$('.redeem_request').val(can_redeem);
		 $('#tot_amt').val(totamt); 
		 $('#tot_sel_amt').html(totamt-can_redeem);
  });
    
    /*$(document).on('click', '.incre_due', function(e){
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
					// Gst calculate
					if($(value).find(".gst_val").val()> 0 && pay_amt>0){
						//var pay_amt = parseFloat(pay_amt)+(parseFloat(due_count)*parseFloat($(value).find(".gst_val").val()));
						var schPayData = {
							'instalment_amt'	: amount,
							'sel_dues' 			: due_count,
							'discount' 			: discountedAmt,
							'gst_type' 			: parseFloat($(value).find(".gst_type").val()),
							'gst_percent' 		: parseFloat($(value).find(".gst").val()),
							'gold_metal_rate'	: parseFloat($(value).find(".metal_rate").val()),
			            	'scheme_type'		: parseFloat($(value).find("#scheme_type").val()),
			            	'flexible_sch_type'	: parseFloat($(value).find("#flexible_sch_type").val())
						}
						var calcData = calculate_payAmt(schPayData);
						var pay_amt = calcData.payment_amt; // Per installment payment amount
					}
	
	            //discount calculate
	            
                    var calc_discount = (accData.allowPayDisc==1?(accData.discount_type==0 ?accData.discount*due_count :(accData.discount_installment==due_count ? accData.discount:0.00)):0.00);
                    
                    if(accData.discount_set==1&&accData.allowPayDisc==1&& calc_discount>0)
                    {
                    var amount =(parseFloat(accData.payable) - parseFloat(calc_discount)).toFixed(0);
                     
                    var discountedAmt = parseFloat(calc_discount).toFixed(0);
                     $(value).find(".discount").val(discountedAmt);
                    }
                    else
                    {
                    var amount = parseFloat(accData.payable);
			        }
	           
	            //dis calculate
	            var discount=(accData.discount_type==0?discountedAmt:((accData.discount_installment==due_count)||($(value).find(".discount").val()>0)?discountedAmt:0.00));
	            
	          
	            var pay_amt = accData.allowPayDisc!=1?((amount*due_count) + (charge*due_count)):(actamt*due_count)+(charge*due_count)-discount;
	               
	           
			  
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
					 show_pay = '<b>'+accData.currency_symbol+' '+pay_amt+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+parseFloat((selweight*due_count)).toFixed(3)+' gm </span></br>';
					
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
				else if($(value).find('#scheme_type').val() == 3)
				{
						
						var amount=parseFloat($(value).find(".pay_amtwgt").val())*due_count;
						
					        var metal_rate  = $(value).find(".metal_rate").val();
                            if(metal_rate!=='' && accData.wgt_convert==0 &&(accData.flexible_sch_type==2 || accData.flexible_sch_type==3))
                            {
                            var selweight = amount/metal_rate;
                            }
                            else{
                            var selweight = '-';
                            }
						 
						    if(accData.wgt_convert==0 && (accData.flexible_sch_type==2 || accData.flexible_sch_type==3))
						    {
                              var show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(amount)+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+parseFloat(selweight).toFixed(3)+' gm </span></br>'; 
                            }
                            else
                            {
                           	  show_pay = '<b>'+accData.currency_symbol+' '+amount+'</b>';
                            }
						 
						 $(value).find(".show_pay").html(show_pay); 
						 
						
				}
				else{
					var charge=parseFloat($(value).find(".charge").val())*due_count;
					//var amount=parseFloat($(value).find(".amount").val());
					    //console.log(charge);
					show_pay = '<b>'+accData.currency_symbol+' '+pay_amt+'</b><br>';
					//console.log(show_pay);
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
				
			 } 
			else if($(value).find(".select_chit").is(":checked"))
			 {
			     
				if(parseFloat($(value).find('.discount').val()) >0 ){
				
					totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))-parseFloat($(value).find('.discount').val()))+($(value).find('.gst_type').val()==1?((parseFloat($(value).find('.sel_due').val()))*parseFloat($(value).find('.gst_val').val())):0));
				}else{
										
					totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0)));
				}
			 }
		});
		// Set total amount and wallet amount 
		 var can_redeem = 0;
		 if($('.ischk_wallet_pay').val() == 1 && parseFloat($(".wallet_balance").val()) > 0){
			 var allowed_redeem =  ($('.redeem_percent').val() > 0 ? (totamt*(parseFloat($('.redeem_percent').val())/100)) : 0);
			 wallet_balance = parseFloat($('.wallet_balance').val());
			 if( allowed_redeem > wallet_balance ){
			 	can_redeem = wallet_balance;
			 }else{
			 	can_redeem = allowed_redeem;
			 }
		 }
		 $('.wallet').val(can_redeem);$('.redeem_request').val(can_redeem);
		 $('#tot_amt').val(totamt); 
		 $('#tot_sel_amt').html(totamt-can_redeem);
				
    });*/
	
	$(document).on('click', '.cal_amt', function(e){
		var cal_amt = ($(this).val());
	    var totamt = 0.00;
	    var firstPayamt_as_payamt=true;
	    $("#scheme-amount tbody tr").each(function(index, value)
		{
		
			if((index+1) == cal_amt)
			{
				
				if($(value).find(".pay_amtwgt").val()!='')
				{
					var payable = parseFloat($(value).find(".pay_amtwgt").val());
					var allowed_dues = parseFloat($(value).find(".allowed_dues").val()); 
					//var maxamount= accData.max_amount!=0 && (accData.max_weight==0 || accData.max_weight!='')? accData.max_amount:accData.max_weight;
					var payment_limit = accData.max_amount!=0 && (accData.max_weight==0 || accData.max_weight!='')?(accData.firstPayamt_as_payamt==1 && accData.paid_installments>0? payable:accData.max_amount): accData.max_amount; //Checking Firstpay amount as payment amount(firstPayamt_as_payamt)    
					//var payment_limit = accData.max_amount!=0 && (accData.max_weight==0 || accData.max_weight!='')?(accData.firstPayamt_as_payamt==1 && accData.paid_installments>0 ? payable:parseFloat(accData.current_total_amount) + parseFloat(payable)): parseFloat((parseFloat(accData.max_weight) - parseFloat(accData.current_total_weight))/$(value).find(".metal_rate").val()); //Checking Firstpay amount as payment amount(firstPayamt_as_payamt)   
					
    				//if((accData.firstPayamt_maxpayable==1 || accData.firstPayamt_as_payamt==1)&&accData.flexible_sch_type==2 && accData.flx_denomintion != null) //For Denomination checking
    				if((accData.firstPayamt_maxpayable==1 || (accData.firstPayamt_as_payamt==1 || accData.firstPayamt_as_payamt==0))&& (accData.flexible_sch_type==2 || accData.flexible_sch_type==1) && accData.flx_denomintion != null) //For Denomination checking
    				{	
    					if(($(value).find(".pay_amtwgt").val())%(accData.flx_denomintion)!=0)
    					{
    						firstPayamt_as_payamt=false;
    					}
    					else
    					{
    						firstPayamt_as_payamt=true;
    						$('#error-msg').html('');
    					}
    				}
		
			        if(firstPayamt_as_payamt==true)
        			{
        				var allowed_dues = parseFloat($(value).find(".allowed_dues").val());  
        				if((accData.max_amount >= payment_limit)&& ( (accData.current_total_weight <= accData.max_amount)||(accData.current_total_amount <= accData.max_amount))&& ((accData.max_chance > accData.current_chances_pay) || (accData.allowed_dues>=allowed_dues)))
        				{
        				    console.log('payable'+payable);
        				    console.log('min_amount'+accData.min_amount);
        				    console.log('max_amount'+accData.max_amount);
        					if(accData.min_amount <= payable && payable <= accData.max_amount)
        					{  
        						var metal_rate  = $(value).find(".metal_rate").val();
        						
        						if(metal_rate!=='' && accData.wgt_convert==0)
        						{
        							var selweight = payable/metal_rate;
        						}
        						else{
        							var selweight = '-'; 
        					    }
        						var due_no = parseInt(accData.current_paid_installments) + 1;
        						
        		 				//calculate discount
        						var calc_discount = (accData.firstPayDisc_by==1?accData.discount:(parseFloat(payable)*(parseFloat(accData.discount)/100)).toFixed(2));
        		 				// check discount settings to calculate discount
        						if(accData.allowPayDisc==1){
        							var amount =(parseFloat(payable) - parseFloat(calc_discount)).toFixed(2);
        							var discountedAmt = parseFloat(calc_discount).toFixed(2);
        						}
        						else{
        							var amount = parseFloat(payable);
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
        				 		var schPayData = {
									'instalment_amt'	: amount,
									'sel_dues' 			: 1,
									'discount' 			: discountedAmt,
									'gst_type' 			: accData.gst_type,
									'gst_percent' 		: accData.gst,
									'is_fixed_weight'   : (parseFloat(accData.scheme_type) == 1 && parseFloat(accData.min_weight) == parseFloat(accData.max_weight)) ? 1 : 0 ,
									'max_weight'        : parseFloat(accData.max_weight),
									//'gold_metal_rate'	: $(value).find(".metal_rate").val(),
									'metal_rate'    	: $(value).find(".metal_rate").val(),
					            	'scheme_type'		: accData.scheme_type,
					            	'flexible_sch_type'	: accData.flexible_sch_type
								}
								var calcData = calculate_payAmt(schPayData);
        						var calc_gst = calcData.gst_amt;
        						//var calc_gst = ((parseFloat(payable)*(parseFloat(accData.gst)/100)).toFixed(0));
        						   
        						if(accData.gst_type==0)
        						{
        
        							var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));
        
        						}
        							
        						if(accData.gst_type==1)
        						{	
        							var payment_amt = Math.round((parseFloat(amount)+parseFloat(calc_gst))+parseFloat(charge));
        						}
        				
        						if(accData.wgt_convert==0 && (accData.flexible_sch_type==2 || accData.flexible_sch_type==3)){
        							var show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(payment_amt)+'</b><span class="btn btn-warning pull-right" style="background-color: #00a65a !important; border-color: #00a65a; ">'+'wgt :'+calcData.metal_weight+' gm </span></br>'; 
        						}
        						else{
        							var show_pay = '<b>'+accData.currency_symbol+' '+parseFloat(payment_amt);
        						}
        
        						if(charge > 0){
        							show_pay = show_pay +'<span style="font-size: 11px;">('+parseFloat(amount)+' + '+parseFloat(charge)+'*)</span></br>';
        						}
        
        						if(discountedAmt >0){
        							show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>";
        						}
        	 //Gst calculate
        						if(calc_gst >0 && accData.gst_type==0){
        				 			show_pay =	show_pay +  "</br><span style='font-size: 11px;'> GST amt inclusive : "+parseFloat(calc_gst)+"</span>";
        				 		}
        				 
        				 		if(calc_gst >0 && accData.gst_type==1){
        				 			show_pay =	show_pay +  "</br><span style='font-size: 11px;'> GST amt exclusive : "+parseFloat(calc_gst)+"</span>";
        				 		}
        				
        						$(value).find(".show_pay").html(show_pay)	
        						$(value).find(".payment_amt").val(payment_amt);
        						$(value).find(".amount").val(amount);
        						$(value).find(".gst_val").val(calc_gst);
        						$(value).find(".gst_type").val(accData.gst_type);
        						$(value).find(".sel_weight").val(calcData.metal_weight);
        						$(value).find(".metal_rate").val(metal_rate);
        						$(value).find(".charge").val(charge);
        						 $(value).find(".discount").val(discountedAmt);
        						$(value).find(".ischecked").val(1);
        						$(value).find(".no_of_due").val(due_no);
        						$(value).find(".sel_due").val(1);
        						$(value).find(".allowed_dues").val(accData.allowed_dues);
        						$(value).find(".actamt").val(payable);
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
        			 
        						 // Set total amount and wallet amount 
        						 var can_redeem = 0;
        						 if($('.ischk_wallet_pay').val() == 1 && parseFloat($(".wallet_balance").val()) > 0){
        							 var allowed_redeem =  ($('.redeem_percent').val() > 0 ? (totamt*(parseFloat($('.redeem_percent').val())/100)) : 0);
        							 wallet_balance = parseFloat($('.wallet_balance').val());
        							 if( allowed_redeem > wallet_balance ){
        							 	can_redeem = wallet_balance;
        							 }else{
        							 	can_redeem = allowed_redeem;
        							 }
        						 }
        				
        						 $('.wallet').val(can_redeem);$('.redeem_request').val(can_redeem);
        						 $('#tot_amt').val(totamt); 
        						 $('#tot_sel_amt').html(totamt-can_redeem); 
        				 }
        				 else if(payable > accData.max_amount){
        					
        					$(value).find(".show_pay").html('');
        					$(value).find(".actamt").val(0);
        					$(value).find(".sel_due").val(0);				
        					$(value).find(".charge").val(0);				
        					$(value).find(".discount").val(0);				
        					$(value).find(".gst_type").val(0);				
        					$(value).find(".gst_val").val(0);
        					$(value).find(".sel_due").val(1);
        					$(value).find(".pay_amtwgt").val(accData.min_amount);
        					
        					var Eligible_payment1 = (accData.max_amount!=0 && (accData.max_weight==0 || accData.max_weight!='')? (((accData.firstPayamt_maxpayable==1 || accData.firstPayamt_as_payamt==1)&&(accData.paid_installments>0) &&(accData.flexible_sch_type==1 || accData.flexible_sch_type==2)) ? parseFloat(accData.firstPayment_amt): (parseFloat(accData.max_amount))):parseFloat(accData.max_weight - accData.current_total_weight));
        			
        					var  Eligible_pay = (accData.max_amount!=0 && (accData.max_weight==0 || accData.max_weight!='')? Eligible_payment1:(parseFloat((parseFloat(accData.max_weight) - parseFloat(accData.current_total_weight))*$(value).find(".metal_rate").val()).toFixed(2)));
        					 
        					$(value).find(".pay_amtwgt").val(Eligible_pay);
        					
        				   msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong> Warning!! </strong> You could not pay more than <strong> Rs '+Eligible_pay+' </strong></div>';
        				        //stop the form from submitting
        				         $('#error-msg').html(msg);
        				  }
        				  else{
        					
        					$(value).find(".show_pay").html('');
        					$(value).find(".actamt").val(0);
        					$(value).find(".sel_due").val(0);				
        					$(value).find(".charge").val(0);				
        					$(value).find(".discount").val(0);				
        					$(value).find(".gst_type").val(0);				
        					$(value).find(".gst_val").val(0);
        					$(value).find(".sel_due").val(1);
        					$(value).find(".pay_amtwgt").val(accData.min_amount);
                            
        					
        				  msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong> Warning! </strong> You could not pay less than <strong> Rs  '+accData.min_amount+' </strong></div>';
        				 
        				 /* msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong> Warning! </strong> You could not pay less than <strong> Rs  '+ Eligible_payment2 +' </strong></div>'; */
        				 
        				        //stop the form from submitting
        				         $('#error-msg').html(msg);
        				  }
        				}
        				else {
            				$(value).find(".show_pay").html('');
            				$(value).find(".actamt").val(0);
            				$(value).find(".sel_due").val(0);				
            				$(value).find(".charge").val(0);				
            				$(value).find(".discount").val(0);				
            				$(value).find(".gst_type").val(0);				
            				$(value).find(".gst_val").val(0);
            				$(value).find(".sel_due").val(1);
            				$(value).find(".pay_amtwgt").val(accData.min_amount);
            			    var Eligible_payment1 = (accData.max_amount!=0 && (accData.max_weight==0 || accData.max_weight!='')? (((accData.firstPayamt_maxpayable==1 || accData.firstPayamt_as_payamt==1)&&(accData.paid_installments>0) &&(accData.flexible_sch_type==1 || accData.flexible_sch_type==2)) ? parseFloat(accData.firstPayment_amt): (parseFloat(accData.max_amount))):parseFloat(accData.max_weight - accData.current_total_weight));
            			    var  Eligible_pay = (accData.max_amount!=0 && (accData.max_weight==0 || accData.max_weight!='')? Eligible_payment1:(parseFloat((parseFloat(accData.max_weight) - parseFloat(accData.current_total_weight))*$(value).find(".metal_rate").val()).toFixed(2)));
                			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong> Warning!!! </strong> You could not pay more than <strong> Rs '+Eligible_pay+' </strong></div>';
            			    //stop the form from submitting
            			    $('#error-msg').html(msg);		  			
        			    }
        			  
        	        }
                	else
                	{
        				$(value).find(".show_pay").html('');
        				$(value).find(".actamt").val(0);
        				$(value).find(".sel_due").val(0);				
        				$(value).find(".charge").val(0);				
        				$(value).find(".discount").val(0);				
        				$(value).find(".gst_type").val(0);				
        				$(value).find(".gst_val").val(0);
        				$(value).find(".sel_due").val(1);
        				$(value).find(".pay_amtwgt").val(accData.min_amount);
                        msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong> Warning! </strong>Please enter Amount in multiples of '+(accData.flx_denomintion)+' </strong></div>';
                        //stop the form from submitting
                        $('#error-msg').html(msg);
                	}
		       }		 
    		}
    	});
	
// new scheme for amount to wgt	
	
	
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
				//$(value).find(".pay_amtwgt").val(0);
				$(value).find(".tot_sel_amt").val(0);
				
				/*alert($('#tot_amt').val());*/
			 } 
			else if($(value).find(".select_chit").is(":checked"))
				 {
					if(parseFloat($(value).find('.discount').val()) >0 ){
						//console.log(parseFloat(totamt));
						totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))-parseFloat($(value).find('.discount').val()))+($(value).find('.gst_type').val()==1?((parseFloat($(value).find('.sel_due').val()))*parseFloat($(value).find('.gst_val').val())):0));
					}else{											
						totamt =(parseFloat(totamt)+(((parseFloat($(value).find('.actamt').val())*parseFloat($(value).find('.sel_due').val()))+(parseFloat($(value).find('.charge').val())*parseFloat($(value).find('.sel_due').val())))+($(value).find('.gst_type').val()==1?(parseFloat($(value).find('.sel_due').val())*parseFloat($(value).find('.gst_val').val())):0)));
					}
				 }
			 });
				//console.log(parseFloat($(value).find('.discount').val()));
			// Set total amount and wallet amount 
			 var can_redeem = 0;
			 if($('.ischk_wallet_pay').val() == 1 && parseFloat($(".wallet_balance").val()) > 0){
				 var allowed_redeem =  ($('.redeem_percent').val() > 0 ? (totamt*(parseFloat($('.redeem_percent').val())/100)) : 0);
				 wallet_balance = parseFloat($('.wallet_balance').val());
				 if( allowed_redeem > wallet_balance ){
				 	can_redeem = wallet_balance;
				 }else{
				 	can_redeem = allowed_redeem;
				 }
			 }
			
			 $('.wallet').val(can_redeem);$('.redeem_request').val(can_redeem);
			 $('#tot_amt').val(totamt); 
			 $('#tot_sel_amt').html(totamt-can_redeem);
				
    });
	
    	
 /*$(document.body).submit(function(e){
		
		var i=0;
		 $("#scheme-amount tbody tr").each(function(index, value){
			
		 	if($(value).find(".select_chit").is(":checked"))
		    {	
		     i=i+1;
			 
			 if($('.wallet_balance').val() >= $('.wallet_payamt').val()){
				
				$(value).find(".redeemed_amt").val($('.wallet_payamt').val());
			 }
			}
		 })
		 		 
		 if(i>0 && $('#tot_amt').val()>0){
			if($('.wallet_balance').val() < $('.wallet_payamt').val() && $('.ischk_wallet_pay').val()=='1'){
				 msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong> Warning! </strong> You could not pay more than <strong> Rs '+$('.wallet_balance').val()+' </strong></div>';
			        //stop the form from submitting
					$('.wallet_payamt').val('');
			         $('#error-msg').html(msg);
			 }
			 else if($('.wallet_balance').val() >= $('.wallet_payamt').val() && $('.ischk_wallet_pay').val()=='1'){
				return true;
			 }else if($('.ischk_wallet_pay').val()=='0'){				 
				return true;
			 }
		 	return false;
		 }
		 else{
			 
		 	  msg='<div class = "alert alert-warning"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please select chit(s) to proceed payment.</div>';
			        //stop the form from submitting
			         $('#error-msg').html(msg);
			       
		 	return false;
		 }
});*/
		
	$(document.body).submit(function(e){
		var i=0;
		$("#scheme-amount tbody tr").each(function(index, value){
			if($(value).find(".select_chit").is(":checked")){	
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
	 
	   var metal_rate = parseFloat($('#metal_rate_value').val()).toFixed(2); 
	    console.log(metal_rate);
		 $("input[name=weight_gold]:checked").each(function() {
		   selected_weight= parseFloat(parseFloat(selected_weight)+ parseFloat($(this).val())).toFixed(3);
		 });
			 
		$('#sel_wt').html(parseFloat(selected_weight).toFixed(3));
		 
 // calc amount for selected weight
		var tot_amt = Math.round(parseFloat(selected_weight) * parseFloat(metal_rate));
		  console.log(tot_amt);
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
			var schPayData = {
					'instalment_amt'	: parseFloat(selected_weight) * parseFloat(metal_rate),
					'sel_dues' 			: 1,
					'discount' 			: discountedAmt,
					'is_fixed_weight'   : (parseFloat(accData.scheme_type) == 1 && parseFloat(accData.min_weight) == parseFloat(accData.max_weight)) ? 1 : 0 ,
					'max_weight'        : parseFloat(accData.max_weight),
					'gst_type' 			: parseFloat(accData.gst_type),
					'gst_percent' 		: parseFloat(accData.gst),
					//'gold_metal_rate'	: parseFloat(accData.metal_rate),
					'metal_rate'        : rate[accData.rate_field],
	            	'scheme_type'		: parseFloat(accData.scheme_type),
	            	'flexible_sch_type'	: parseFloat(accData.flexible_sch_type)
				}
			var calcData = calculate_payAmt(schPayData);
			var calc_gst = calcData.gst_amt;
			//var calc_gst = (((parseFloat(selected_weight) * parseFloat(metal_rate))*(parseFloat(accData.gst)/100)).toFixed(0));
			var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));
			if(accData.gst_type==0)
			{
				var payment_amt = Math.round(parseFloat(amount)+parseFloat(charge));
			    
			}
				
			if(accData.gst_type==1)
			{	
				var payment_amt = Math.round((parseFloat(amount)+parseFloat(calc_gst))+parseFloat(charge));
			    
			} 
		 
		 
	     
		
	  var  show_pay="<b>"+$('#currency_symbol').val()+' '+parseFloat(payment_amt)+'</b><button type="button" value="'+parseFloat($('#id_scheme_account').val())+'"  class="btn btn-small btn-warning chg_wgt pull-right">'+parseFloat(selected_weight).toFixed(1)+' g Change</button><br/>';		
			
		 if(charge > 0){
			 show_pay = show_pay +'<span style="font-size: 11px;">('+parseFloat(payment_amt)+' + '+parseFloat(charge)+'*)</span></br>';
		 }
		
		 if(discountedAmt >0){
		 show_pay =	show_pay +  "<span style='font-size: 11px;'> Discount : "+parseFloat(discountedAmt)+"</span>"
		 }
		 // Gst calculate	
			/* if(calc_gst >0 && accData.gst_type==0){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt inclusive : "+parseFloat(calc_gst)+"</span>"} */
			 
			 if(calc_gst >0 && accData.gst_type==1){
			 show_pay =	show_pay +  "<span style='font-size: 11px;'> GST amt exclusive : "+parseFloat(calc_gst)+"</span>"}
		    console.log(show_pay);
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
			 
			 // Set total amount and wallet amount 
			 var can_redeem = 0;
			 if($('.ischk_wallet_pay').val() == 1 && parseFloat($(".wallet_balance").val()) > 0){
				 var allowed_redeem =  ($('.redeem_percent').val() > 0 ? (totamt*(parseFloat($('.redeem_percent').val())/100)) : 0);
				 wallet_balance = parseFloat($('.wallet_balance').val());
				 if( allowed_redeem > wallet_balance ){
				 	can_redeem = wallet_balance;
				 }else{
				 	can_redeem = allowed_redeem;
				 }
			 }
			 $('.wallet').val(can_redeem);$('.redeem_request').val(can_redeem);
			 $('#tot_amt').val(totamt); 
			 $('#tot_sel_amt').html(totamt-can_redeem);
		 }
	  });
				
   });
   
});

payment = {
	setcontent:function(accData){
		
		console.log(rate[accData.chit.rate_field]);
		
			    var chit = accData.chit;
	  			var weight = accData.weights;
			    var rate  = accData.metal_rates;
			    var customer = accData.customer;
			    var eligible_weight= parseFloat(chit.max_weight).toFixed(3) - parseFloat(chit.current_total_weight).toFixed(3);
			    
			    var content ="<div class='pay-content'><h3>"+(chit.chit_number!=''?chit.chit_number:chit.scheme_name)+"</h3><p>Select weight to make payment<p><div id='error-msg'></div>  <input type='hidden' id='allowPayDisc'  value='"+(chit.allowPayDisc)+"'/><input type='hidden' id='firstPayDisc_by'  value='"+(chit.firstPayDisc_by)+"'/><input type='hidden' id='discount'  value='"+(chit.discount)+"'/><input type='hidden' id='charge'  value='"+(chit.charge_type)+"'/><input type='hidden' id='charge'  value='"+(chit.charge)+"'/><input type='hidden' id='id_scheme_account'  value='"+(chit.id_scheme_account)+"'/><input type='hidden' id='currency_symbol'  value='"+(chit.currency_symbol)+"'/></div>"
			    
				var pay = "<input type='hidden' id='metal_rate_value' name='pay[udf3]' value='"+(chit.scheme_type==1?parseFloat(rate[accData.chit.rate_field]).toFixed(2):'')+"'/>"
						 // +"<h4>Payment Amount : Rs. <span id='tot_amt'>"+(chit.scheme_type==0?parseFloat(chit.payable).toFixed(2):'0.00')+"</span> </h4>";
			 	
			 	
			    var weight_check='<div class="rate-table"><table class="table table-bordered table-striped table-responsive text-center">'+
			       '<tr><th colspan="3" style="text-align:center" ><h3 > Gold 22k 1gm rate : '+parseFloat(rate[accData.chit.rate_field]).toFixed(2)+'</h3></th></tr>'+
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
	
	

	$(document).on('change', '.ischk_wallet_pay',function(ev){
		if($('.ischk_wallet_pay').is(':checked')){
			/*$(".wallet_payamt").prop('disabled',false);
			$('.wallet_payamt').val($('.wallet_balance').val());*/
			$('.use_wallet').val(1);
			$('.ischk_wallet_pay').val(1);
		 }else{
			 /*$(".wallet_payamt").prop('disabled',true);
			 $('.wallet_payamt').val('');*/
			 $('.use_wallet').val(0);
			 $('.ischk_wallet_pay').val(0);
		 }
		
	})
	
	function calculate_payAmt(schData){
		console.log(schData);
        var gst_percent = schData.gst_percent == '' ? 0 : parseFloat(schData.gst_percent);
        var metal_rate = parseFloat(schData.metal_rate);
        console.log(metal_rate);
        var gst = 0;
        var gst_type = schData.gst_type == '' ? 0 : parseFloat(schData.gst_type);
		var sel_dues = schData.sel_dues == '' ? 1 : parseFloat(schData.sel_dues);
		var discount = schData.discount == '' ? 0 : parseFloat(schData.discount); 
		var scheme_type = parseFloat(schData.scheme_type);
		//console.log(gst_percent+"-"+metal_rate+"-"+gst_type+"-"+sel_dues+"-"+discount);
        var metal_weight = 0;
        var weight = 0;
        var instalment_amt = parseFloat(schData.instalment_amt);
        var insAmt_withoutDisc = instalment_amt - discount;
        var gst_amt = 0;
        if(gst_percent > 0){
            if(gst_type == 0){ 
                // Inclusive
            	var gst_removed_amt = insAmt_withoutDisc*100/(100+gst_percent); 
            	gst_amt = insAmt_withoutDisc - gst_removed_amt;
            	// Set Value
            	if((schData.flexible_sch_type == 2 || schData.flexible_sch_type == 3) || (scheme_type == 2 || scheme_type == 3 )){
            	    metal_weight = (gst_removed_amt+discount)/metal_rate;
            	    weight = setMetalWgt(metal_weight);
            	}
            	else if(scheme_type == 1  && schData.is_fixed_weight == 0){
                    metal_weight = $('#selected_weight').val();
                    weight = setMetalWgt(metal_weight);
            	}
            	else if(scheme_type == 1 && schData.is_fixed_weight == 1){
                    metal_weight = schData.max_weight;
                    setMetalWgt(metal_weight);
            	}
            	/*$('#payment_weight').val(metal_weight*sel_dues);
                $('#gst_amt').val(gst_amt*sel_dues); 
                $('#payment_amt').val(insAmt_withoutDisc*sel_dues); */
                
                console.log({"gst_removed_amt" : gst_removed_amt, "gst_amt" : gst_amt, "metal_weight" : weight});
            	return {"payment_amt":insAmt_withoutDisc,"gst_removed_amt" : gst_removed_amt, "gst_amt" : gst_amt, "metal_weight" : weight};
            }
            else if(gst_type == 1){ 
                // Exclusive
            	var amt_with_gst = insAmt_withoutDisc*((100+gst_percent)/100);
            	gst_amt = amt_with_gst - insAmt_withoutDisc ; 
            	// Set Value
            	if(schData.flexible_sch_type == 2 || scheme_type == 2 ){
            	    metal_weight = instalment_amt/metal_rate ;
            	    weight = setMetalWgt(metal_weight);
            	}
            	else if(scheme_type == 1  && schData.is_fixed_weight == 0){
                    metal_weight = $('#selected_weight').val();
                    weight = setMetalWgt(metal_weight);
            	}
            	else if(scheme_type == 1 && schData.is_fixed_weight == 1){
                    metal_weight = schData.max_weight;
                    weight = setMetalWgt(metal_weight);
            	}
            	/*$('#payment_weight').val(metal_weight*sel_dues);
            	$('#gst_amt').val(gst_amt*sel_dues); 
            	$('#payment_amt').val(amt_with_gst*sel_dues); */
            	console.log({"amt_with_gst" : amt_with_gst, "gst_amt" : gst_amt, "metal_weight" : metal_weight});
            	return {"payment_amt":amt_with_gst,"amt_with_gst" : amt_with_gst, "gst_amt" : gst_amt, "metal_weight" : weight};
            } 
        }else{        	
            if(schData.flexible_sch_type == 2 || scheme_type == 2 ){
                metal_weight = instalment_amt/metal_rate ;
                weight = setMetalWgt(metal_weight);
        	}
        	else if(scheme_type == 1 && schData.is_fixed_weight == 0){
                metal_weight = $('#selected_weight').val();
                weight = setMetalWgt(metal_weight);
        	}
        	else if(scheme_type == 1 && schData.is_fixed_weight == 1){
                metal_weight = schData.max_weight;
                weight = setMetalWgt(metal_weight);
        	}
        	/*$('#payment_weight').val(metal_weight*sel_dues);
        	$('#gst_amt').val(gst_amt*sel_dues); 
        	$('#payment_amt').val(insAmt_withoutDisc*sel_dues); */
        	return {"payment_amt":insAmt_withoutDisc, "gst_amt" : gst_amt, "metal_weight" : weight};
        }
        
        /*GST Inclusive :
        ===============
        Gold Rate = 5000
        Installment amount = Rs. 500
        Amount Inclusive of GST = Rs. 490
        Discount = Rs. 10
        GST rate = 3%  
        Payment Amount = 490
        Remove GST = 490*100/(100+3) = 475.7281553398058
        GST 3% = 490 - 475.7281553398058 = 14.2718446601942
        Weight = (475.7281553398058 +10)/5000 = 0.0971456310679612
        
        GST Exclusive :
        ===============
        Gold Rate = 5000
        Installment amount = Rs. 500
        Amount Exclusive of GST = Rs. 490
        GST rate = 3%  
        Payment Amount = 490*((100+3)/100) = 504.7
        GST 3% = 504.7 - 490 = 14.7
        Weight = 500/5000 = 0.1*/
    }
        
    function setMetalWgt(metal_wgt)
    {
      var metal_weight = metal_wgt;
      var metal_wgt_roundoff = $("#metal_wgt_roundoff").val();
      var metal_wgt_decimal = $("#metal_wgt_decimal").val(); 
      let isnum = /^\d+$/.test(metal_wgt); 
      console.log(metal_weight +'--'+ isnum);
      if(metal_wgt_roundoff == 0 && isnum == false && metal_wgt != ""){
          var arr = metal_weight.split(".");  
          var str = arr[1];
          var deci = str.substring(0, metal_wgt_decimal); // Take first 2 decimal places
          console.log(deci);
          return arr[0]+"."+deci;
      }else{
          return parseFloat(metal_wgt).toFixed(metal_wgt_decimal);
      }
    }