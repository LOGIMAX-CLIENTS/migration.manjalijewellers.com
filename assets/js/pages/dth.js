$(document).ready(function() {
	get_branch_metalrates(1);
  $("#name").on('keypress',function(event){    
    var regex= new RegExp("^[a-zA-Z ]*$");    
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }    
    });
  $("#mobile").on('keypress',function(event){    
    var regex= new RegExp("^[a-zA-Z ]*$");    
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }    
    });
    
      $("#email").on('keypress',function(event){    
    var regex= new RegExp("^[a-zA-Z ]*$");    
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }    
    });
    
	
	
	$( "#dth_submit" ).click(function( event ) {
		event.preventDefault();
		//contact.validate_form();
	});

/*	$( "#btnsubmit" ).click(function( event ) {


		event.preventDefault();


		enquiry.validate_form();


	});


	$( "#refCaptcha" ).click(function(event) {


		event.preventDefault();


		enquiry.refCaptcha();


	});


	$("#chit-desc").click(function(event) {


		event.preventDefault();


	});*/


});





}

function get_branch_metalrates(id_branch)
{ 
	 
	
	my_Date = new Date();
	$("div.overlay").css("display", "block"); 
	$.ajax({
		type: "POST",	
		url:baseURL+ "index.php/user/get_branch_rate?nocache=" + my_Date.getUTCSeconds(),
		data: {'id_branch':id_branch},			 
		dataType: 'json',			
		success:function(data){ 
			var discSettings = data.cmpy_detail;
			var rate = data.metal_rate;
			
			var chk = jQuery.isEmptyObject(rate);
			console.log(chk);
			if(rate.length > 0){
				// Header
				$('#metal_rate').append($('<tr>')
				    .append($('<td class="silver rt-bdr" >').append("Today's Rate"))
				    .append($('<td class="gold rt-bdr" id="td_gold18">').append("Gold 18CT"))
				    .append($('<td class="gold rt-bdr" id="td_gold22">').append("Gold 22CT"))
				    .append($('<td class="gold rt-bdr" id="td_gold24">').append("Gold 24CT"))
				    .append($('<td class="silver rt-bdr" id="td_silver">').append("Silver"))
				    .append($('<td class="platinum" id="td_plat">').append("platinum"))
				)
			    // Shop rate 			 
				$('#metal_rate').append($('<tr>')
				    .append($('<td class="rt-bdr" style="text-align:center;">').append((discSettings.enableGoldrateDisc=1) || (discSettings.enableSilver_rateDisc =='1' ) ?'Our Rate (1g)':'1 Gram'))
				    .append($('<td class="rt-bdr" id="col_goldrate_18ct" style="text-align:center;">').append(rate.goldrate_18ct>0?rate.goldrate_18ct:'NA')) 
				    .append($('<td class="rt-bdr" id="col_goldrate_22ct" style="text-align:center;">').append(rate.goldrate_22ct>0?rate.goldrate_22ct:'NA'))
				    .append($('<td class="rt-bdr" id="col_goldrate_24ct" style="text-align:center;">').append(rate.goldrate_24ct>0?rate.goldrate_24ct:'NA'))
				    .append($('<td class="rt-bdr" id="col_silverrate_1gm" style="text-align:center;">').append(rate.silverrate_1gm>0?rate.silverrate_1gm:'NA'))
				    .append($('<td class="rt-bdr" id="col_platinum_1g" >').append(rate.platinum_1g>0?rate.platinum_1g:'NA'))
			  	)
			  
				if(rate.goldrate_18ct<=0) {
					$('#col_goldrate_18ct').remove();
					$('#td_gold18').remove();				
				}
				if(rate.goldrate_22ct<=0){
					
					 $('#col_goldrate_22ct').remove(); 
					 $('#td_gold22').remove();
					
				}
				if(rate.goldrate_24ct<=0){
					
					$('#col_goldrate_24ct').remove();
					$('#td_gold24').remove();
						
				}
				if(rate.silverrate_1gm <=0){
					
					$('#col_silverrate_1gm').remove();
					$('#td_silver').remove();
							
				}
				if(rate.platinum_1g<=0 ){
					
					$('#col_platinum_1g').remove(); 
					$('#td_plat').remove(); 
				}
						
				if(discSettings.enableGoldrateDisc==1 || discSettings.enableSilver_rateDisc==1){	
					var g_rate = (rate.mjdmagoldrate_22ct == 0 ? rate.goldrate_22ct:rate.mjdmagoldrate_22ct);
					$('#metal_rate').append($('<tr>')
					.append($('<td class="rt-bdr" style="text-align:center;">').append('Market Rate (1g)'))
					.append($('<td class="rt-bdr" style="text-align:center;">').append(g_rate))
					)
				}
			}else{
				"No rates";
			}
			
					
		 }

	});	
}