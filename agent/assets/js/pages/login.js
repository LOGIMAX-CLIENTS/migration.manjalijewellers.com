function url_params()
	{
		var url = window.location.href;
		var path=window.location.pathname;
		var params = path.split( 'php/' );
		
		return {'url':url,'pathname':path,'route':params[1]};
	}

var path =  url_params();

var ctrl_page = path.route.split('/');

$(document).ready(function(){
	
	 //branch wise search options in user//hh
	 		if(ctrl_page[1]=='add')
	{
		$.each(branchListArr, function(key, val)
		{
			branchList.push({'label' : val.name, 'value' : val.id_branch});
		});
       
		$( "#store" ).autocomplete(
		{
        source: branchList,
        select: function(e, i)
        {
            e.preventDefault();
            $("#id_branch" ).val(i.item.value);
        },
        minLength: 4,
		});
	}	
 //branch wise search options in user//hh
	$("#username").focus(); 
	
	var branch_settingss= $('#branch_settingss').val();
	//console.log(branch_settingss);
	
	if(branch_settingss == 1){

	$("#select_branch,.select_branch").select2().on("change", function(e) {
		if(this.value!='')
		{   
			var branch_id=$('#branch_id').val();
		    $(".metalrate").find("tr").remove();
			get_branch_metalrates(this.value);
		}
	});
 
	$('#branch_selec').select2().on("change", function(e) {
		if(this.value!='')
		{
			var branch_id=$('#branch_id').val();
		    $(".metalrate").find("tr").remove();
			get_branch_metalrates(this.value);
		}
		
	});
	 	get_branchs();	
	}

});


function get_branch_metalrates(branch_id="",enableGoldrateDisc="",enableSilver_rateDisc="")
{ 
	var enableGoldrateDisc = enableGoldrateDisc;
	var enableSilver_rateDisc = enableSilver_rateDisc;
	my_Date = new Date();
	$("div.overlay").css("display", "block"); 
	$.ajax({
		type: "POST",	
		url:baseURL+ "index.php/user/get_branch_rate?nocache=" + my_Date.getUTCSeconds(),
		data: {'id_branch':branch_id},			 
		dataType: 'json',			
		success:function(data){ 
			var discSettings = data.discSettings;
			var rate = data.metal_rate; 
			var isEmpty = jQuery.isEmptyObject(rate);
			if(typeof rate.goldrate_22ct != 'undefined'){
                // Header
                $('.metalrate').append($('<tr>')
//                    .append($('<td class="silver rt-bdr" >').append("Branch"))
                    .append($('<td class="silver rt-bdr" >').append("Today's Rate"))
                    .append($('<td class="gold rt-bdr" id="td_gold18">').append("Gold 18CT"))
                    .append($('<td class="gold rt-bdr" id="td_gold22">').append("Gold 22CT"))
                    .append($('<td class="gold rt-bdr" id="td_gold24">').append("Gold 24CT"))
                    .append($('<td class="silver rt-bdr" id="td_silver">').append("Silver"))
                    .append($('<td class="platinum" id="td_plat">').append("platinum"))
                )
                // Shop rate 			 
                $('.metalrate').append($('<tr>')
                    .append($('<td class="rt-bdr" style="text-align:center;">').append((discSettings.enableGoldrateDisc=1) || (discSettings.enableSilver_rateDisc =='1' ) ?'Our Rate (1g)':'1 Gram'))
                    .append($('<td class="rt-bdr" id="col_goldrate_18ct" style="text-align:center;">').append(rate.goldrate_18ct>0?rate.goldrate_18ct:'NA')) 
                    .append($('<td class="rt-bdr" id="col_goldrate_22ct" style="text-align:center;">').append(rate.goldrate_22ct>0?rate.goldrate_22ct:'NA'))
                    .append($('<td class="rt-bdr" id="col_goldrate_24ct" style="text-align:center;">').append(rate.goldrate_24ct>0?rate.goldrate_24ct:'NA'))
                    .append($('<td class="rt-bdr" id="col_silverrate_1gm" style="text-align:center;">').append(rate.silverrate_1gm>0?rate.silverrate_1gm:'NA'))
                    .append($('<td class="rt-bdr" id="col_platinum_1g" >').append(rate.platinum_1g>0?rate.platinum_1g:'NA'))
                )
                
                if(parseFloat(rate.goldrate_18ct)<=0) {
                    $('.metalrate #col_goldrate_18ct').remove();
                    $('.metalrate #td_gold18').remove();
                }
                if(rate.goldrate_22ct<=0){
                    $('.metalrate #col_goldrate_22ct').remove(); 
                    $('.metalrate #td_gold22').remove();
                }
                if(rate.goldrate_24ct<=0){
                    $('.metalrate #col_goldrate_24ct').remove();
                    $('.metalrate #td_gold24').remove();
                }
                if(rate.silverrate_1gm <=0){
                    $('.metalrate #col_silverrate_1gm').remove();
                    $('.metalrate #td_silver').remove();
                }
                if(rate.platinum_1g<=0 ){
                    $('.metalrate #col_platinum_1g').remove(); 
                    $('.metalrate #td_plat').remove(); 
                }
                // Market Rate if discount enabled		
                if((rate.mjdmagoldrate_22ct > 0 && rate.mjdmagoldrate_22ct != rate.goldrate_22ct) || (rate.mjdmasilverrate_1gm > 0 && rate.mjdmasilverrate_1gm != rate.silverrate_1gm))
                {
                    if(discSettings.enableGoldrateDisc==1 || discSettings.enableSilver_rateDisc==1){	
                        var g_rate18 = (rate.market_gold_18ct > 0 ? rate.market_gold_18ct:rate.market_gold_18ct);
                        var g_rate22 = (rate.mjdmagoldrate_22ct > 0 ? rate.mjdmagoldrate_22ct:rate.goldrate_22ct);
                        var s_rate1gm = (rate.mjdmasilverrate_1gm > 0 ? rate.mjdmasilverrate_1gm:rate.silverrate_1gm);
                        
                        $('.metalrate').append($('<tr>')
                            .append($('<td class="rt-bdr" style="text-align:center;">').append('Market Rate (1g)'))
                            .append($('<td class="rt-bdr" id="g_rate18" style="text-align:center;">').append(g_rate18))
                            .append($('<td class="rt-bdr" id="g_rate22" style="text-align:center;">').append(g_rate22))
                            .append($('<td class="rt-bdr" id="goldrate_24ct" style="text-align:center;">').append(rate.goldrate_24ct))
                            .append($('<td class="rt-bdr" id="s_rate1gm" style="text-align:center;">').append(s_rate1gm))
                            .append($('<td class="rt-bdr" id="platinum_1g" style="text-align:center;">').append(rate.platinum_1g))
                        )
                        if(rate.goldrate_18ct<=0) {
                            $('#g_rate18').remove();
                        }
                        if(rate.goldrate_22ct<=0){
                            $('#g_rate22').remove(); 
                        }
                        if(rate.goldrate_24ct<=0){
                            $('#goldrate_24ct').remove();
                        }
                        if(rate.silverrate_1gm <=0){
                            $('#s_rate1gm').remove();
                        }
                        if(rate.platinum_1g<=0 ){
                            $('#platinum_1g').remove(); 
                        }
                    }
                }
			}		
		 }

	});	
}



function get_branchs()

{ 
	$(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		url: baseURL+'index.php/user/get_branch/', 
		dataType:'json',
		success:function(data){
		
			var sessionValue= $("#hdnSession").data('value');
			var scheme_val = data[0].id_branch;
			 $.each(data, function (key, item) {	

			var branch_id= $('#branch_id').val();
			
			
         //	var select_default_branch_id   = data[0].branch_id;		   
			  
			   		$("#select_branch,.select_branch").append(
					$("<option></option>")
					.attr("value", item.id_branch)						  
					.text(item.name ));
					
					selectid=$('#branch_id').val();
					$('#branch_select1').append(
					$("<option></option>")
					.attr("value", item.id_branch)						  
					.text(item.name ));			   				
		
				});
		 
		   
			$( "#select_branch,.select_branch" ).append(data.id_branch);
			
			$("#select_branch,.select_branch").select2({
			    placeholder: "Select branch name",
			    allowClear: true });
				
			$("#branch_select1").select2({
			    placeholder: "Select branch name",
			    allowClear: true  });
			
				
			if(sessionValue!=''){
				$("#select_branch,.select_branch").select2("val",(sessionValue!='' && sessionValue>0?sessionValue:''));
			}else{
			 $("#select_branch,.select_branch").select2("val",(scheme_val!='' && scheme_val>0?scheme_val:''));
				 }
			 $(".overlay").css("display", "none");	
		}
	});
}


$('#select_branch').select2().on("change", function(e) { 
	if(this.value!='')
	{   
	

		$("#branch_id").val(this.value);    
		var branch_id=$("#branch_id").val(); 
			
	}
	else
	{   
	$("#branch_id").val('');       
	}
});
 
//dth type vaildation

function valthisform(){
	
 var chkd = document.dth_form.type_DTH.checked || document.dth_form.type_EC.checked

 if (chkd == true){
	 
	  $("#dth_form").submit(function(e){
                 e.currentTarget.submit();
            });
	
	 

 } else {
    alert ("please check a checkbox")
	
	 $("#dth_form").submit(function(e){
                e.preventDefault();
            });
 }
}
//dth type vaildation




