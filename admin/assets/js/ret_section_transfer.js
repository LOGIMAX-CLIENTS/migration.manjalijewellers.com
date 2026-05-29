/*



--Created by VijayKumar--



-- Created on 05-01-22--



--Worked on new flow section transfer tags



*/







var path =  url_params();



var ctrl_page 		= path.route.split('/');



var SectionTagData = [];





$(document).ready(function() {



	var path =  url_params();



	$('#status').bootstrapSwitch();



    $(window).scroll(function() {    // this will work when your window scrolled.



		var height = $(window).scrollTop();  //getting the scrolling height of window



		if(height  > 300) {



			$(".stickyBlk").css({"position": "fixed"});



		} else{



			$(".stickyBlk").css({"position": "static"});



		}



	}); 



	switch(ctrl_page[1]) {



	 	case 'ret_section_transfer':



			switch(ctrl_page[2]) {



				case 'list':

                    

                    let id_branch = ($('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : $("#branch_select").val());



                    get_ActiveSections(id_branch);



                    get_ActiveProduct();



				break;



		}



	}



	



});







function get_ActiveProduct() {



	$('#prod_select option').remove();



	$.ajax({



		url: base_url + 'index.php/admin_ret_reports/get_ActiveProduct',



		data: ({ 'id_category': '' }),



		dataType: "JSON",



		type: "POST",



		success: function (data) {



			$.each(data, function (key, item) {



				$("#prod_select,#prod_filter").append(



					$("<option></option>")



						.attr("value", item.pro_id)



						.text(item.product_name)



				);



			});



			$("#prod_select,#prod_filter").select2(



				{



					placeholder: "Select Product",



					allowClear: true



				});



			$("#prod_select").select2("val", "");

		}

	});

}





$('#branch_select').on('change',function(key,items)



{



    if(this.value!='')



    {



        get_ActiveSections(this.value);

    }

})





function get_ActiveSections(id_branch)



{



    if(id_branch > 0) {



        $("#select_frm_section option").remove();



        $("#select_to_section option").remove();



        my_Date = new Date();



        $.ajax({



            type: 'POST',



            url: base_url+"index.php/admin_ret_catalog/get_sectionBranchwise?nocache=" + my_Date.getUTCSeconds(),



            data:{'id_branch':id_branch},



            dataType:'json',



            success:function(data){

                
                $("#select_frm_section,#select_to_section").append(
                    $("<option></option>")
                    .attr("value",0)
                    .text("All")
                );

                $.each(data,function(key, item){



                    $("#select_frm_section,#select_to_section").append(



                        $("<option></option>")



                        .attr("value",item.id_section)



                        .text(item.section_name)



                    );



                });



                $('#select_frm_section').select2({



                    placeholder:"Select From Section",



                    allowClear: true



                });







                $('#select_to_section').select2({



                    placeholder:"Select To Section",



                    allowClear: true



                });



                



                $("#select_frm_section").select2("val","");







                $("#select_to_section").select2("val","");



                



                $(".overlay").css("display","none");

            }

        });

    }

}









$('#section_tag_search').on('click',function()    

{

    var trans_type =  $("input[name='section_item_type']:checked").val();

    if($('#branch_select').val()==null && $('#branch_filter').val()==undefined)   // condition to check whether branch is selected.

    {

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Select Branch..'});

        $('#select_to_section').focus();

    }

    else if($('#prod_select').val()=="" || $('#prod_select').val()==null)   // condition to check whether section is selected.

    {

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Select Product..'});

    }

    else

    {

        if(trans_type==1)

        {

            getSectionTags();

        }else{

            getNonTaggedItem();
        }
    }
});


let typingTimer;
let typingDelay = 500;

$('#tag_code, #tag_code_old').on('input',()=> {

    clearTimeout(typingTimer);

    typingTimer = setTimeout(() => {
        const tag_code_length = $('#tag_code').val().trim().length;
        const tag_code_old_length = $('#tag_code_old').val().trim().length;
        const shouldTriggerSearch = (tag_code_length >= 4 || tag_code_old_length >= 4);
    
        if(!shouldTriggerSearch) return;
        
        $('#section_tag_search').trigger('click');

    }, typingDelay);

});



function getSectionTags()   // Function that gets tag Section Wise 



{



    //$('#section_trans_list > tbody').empty();



    $(".overlay").css("display", "block");



    my_Date = new Date();



    $.ajax({



        type: 'POST',



        url: base_url+"index.php/admin_ret_section_transfer/ret_section_transfer/getSectionTags?nocache=" + my_Date.getUTCSeconds(),



        dataType:'json',



        data: ({'id_branch':($('#branch_filter').val()!='' && $('#branch_filter').val()!=undefined ? $('#branch_filter').val(): $("#branch_select").val()),'id_section':$('#select_frm_section').val(),'tag_code':$('#tag_code').val(),'old_tag_id':$('#tag_code_old').val(),'est_no':$('#est_no').val(),'id_product':$('#prod_select').val()}),



        success:function(data)



        {



            var list=data;



            console.log(list);







            console.log((list!=null && list.length > 0));







            if(list!=null && list.length > 0)



            {



                var html="";



                $.each(list,function(key,val)



                {



                    var allow_submit = true;







                    $('#section_trans_list > tbody > tr').each(function(idx, row){



                        if(val.tag_id==$(this).find('.tag_id').val())



                        {



                            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists..'});



                            allow_submit=false;



                        }

                    });
                    if(val.orderid != '' && val.orderno != ''){

                        var orderno = val.orderno;

                        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag is reserved for the order "' + orderno + '".',
                            settings: {
                                timeout: 5000 
                            }
                    });

                        allow_submit = false;

                    }

                    if(allow_submit)



                    {



                        



                        html+='<tr>'+



                            '<td><input type="checkbox" name="tag_id[]" class="tag_id" value='+val.tag_id+'></td>'+



                            '<td><input type="hidden" name="id_branch[]" class="id_branch" value='+val.id_branch+'>'+val.branch_name+'</td>'+



                            '<td><input type="hidden" name="tag_code[]" class="tag_code" value='+val.tag_code+'>'+val.tag_code+'</td>'+



                            '<td><input type="hidden" name="old_tag_id[]" class="old_tag_id" value='+val.old_tag_id+'>'+val.old_tag_id+'</td>'+



                            '<td><input type="hidden" name="frm_id_section[]" class="frm_id_section" value='+val.id_section+'>'+val.section_name+'</td>'+



                            '<td><input type="hidden" name="pro_id[]" class="pro_id" value='+val.product_id+'>'+val.product_name+'</td>'+



                            '<td><input type="hidden" name="piece[]" class="piece" value='+val.piece+'>'+val.piece+'</td>'+



                            '<td><input type="hidden" name="gross_wt[]" class="gross_wt" value='+val.gross_wt+'>'+val.gross_wt+'</td>'+



                            '<td><input type="hidden" name="net_wt[]" class="net_wt" value='+val.net_wt+'>'+val.net_wt+'</td>'+



                        '</tr>';             



                    }



                });







                if($('#section_trans_list > tbody  > tr').length>0)



                {



                    $('#section_trans_list > tbody > tr:first').before(html);



                }else{



                    $('#section_trans_list tbody').append(html);



                }



   



                $('#tag_code').val("");   



                $('#tag_code_old').val("");



                $('#est_no').val("");



                $('#select_frm_section').select2('val',""); 



                



                calculateSectiontotal();



            



            }



            else



            {



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});



                



            }



            $(".overlay").css("display", "none");



        



        },



        error:function(error)  



        {



        $("div.overlay").css("display", "none");



        }



    });







}







$('#select_all').click(function(event)     // Select All checkbox click function



{



    $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));



	event.stopPropagation();







    calculateSectiontotal();

});





$(document).on('click',".tag_id", function(){



    calculateSectiontotal();

});	





function calculateSectiontotal()



{



    var tot_pcs = 0;



    var tot_gwt = 0;



    var tot_nwt = 0;



    $("#section_trans_list tbody > tr").each(function () { 



        var row = $(this).closest('tr'); 



        tot_pcs = tot_pcs + (isNaN(row.find('td:eq(6) .piece').val() ) ? 0 : parseFloat(row.find('td:eq(6) .piece').val())); 



        tot_gwt = tot_gwt + (isNaN( row.find('td:eq(7) .gross_wt').val() ) ? 0 : parseFloat(row.find('td:eq(7) .gross_wt').val()));



        tot_nwt = tot_nwt + (isNaN( row.find('td:eq(8) .net_wt').val() ) ? 0 :parseFloat(row.find('td:eq(8) .net_wt').val())) ;             



    });  



    $(".pcs").html(tot_pcs);



    $(".grs_wt").html(parseFloat(tot_gwt).toFixed(3));



    $(".net_wt").html(parseFloat(tot_nwt).toFixed(3));



}






$('#section_transfer').on('click',function()   // Transfer Section button 

{

    $('#section_transfer').prop('disabled',true);

	if($('#select_to_section').val()=="" || $('#select_to_section').val()==null)

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Select Transfer To Section..'});

		$('#select_to_section').focus();

		$('#section_transfer').prop('disabled',false);

	}

	else

	{

		

		var trans_type =  $("input[name='section_item_type']:checked").val();

		if(trans_type == 1)

		{

			$("#section_trans_list tbody tr").each(function (index, value) 

			{

				var row = $(this).closest('tr'); 

				if(row.find("input[name='tag_id[]']:checked").is(":checked"))  // whether checkbox is selected

				{

					SectionTagData.push({"tag_id":row.find('.tag_id').val(),"id_branch":row.find('.id_branch').val(),"trans_from_section":row.find('.frm_id_section').val(),"pcs":row.find('.piece').val(),"grs_wt":row.find('.gross_wt').val(),"net_wt":row.find('.net_wt').val()});

				   

				}

			});

		}

		else if(trans_type == 2)

		{

			$("#bt_nt_search_list tbody tr").each(function (index, value) 

			{

				var row = $(this).closest('tr'); 

				if(row.find("input[name='nt_item_sel[]']:checked").is(":checked"))  // whether checkbox is selected

				{

					SectionTagData.push({"id_nontag_item":row.find('.id_nontag_item').val(),"branch":$('#branch_select').val(),"id_section":row.find('.id_section').val(),"product":row.find('.product').val(),"design":row.find('.design').val(),"id_sub_design":row.find('.id_sub_design').val(),"no_of_piece":row.find('.nt_piece').val(),"gross_wt":row.find('.nt_gross_wt').val(),"net_wt":row.find('.nt_net_wgt').val()});

				}

			});

				  

		}

		if(SectionTagData.length>0)

		{

            if($('#allow_order_item_cancel_otp').val()==1){



                $('.cancel_otp').css('display','none');

                $('.cancel_otp_confirmation').css('display','block');

                $('.verify_otp').css('display','none');

                $('#confirm-sec_transotp').modal('toggle');

    

               }else{

                add_to_trans(SectionTagData);

               }

		}

		else

		{

		    $('#section_transfer').prop('disabled',false);

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please select tag code to proceed..'});
		}
	}
});





function add_to_trans(trans_data)

{

    $(".overlay").css("display", "block");		

    var postData = {};

    var branch = $('#branch_filter').val()!='' && $('#branch_filter').val()!=undefined ? $('#branch_filter').val(): $("#branch_select").val();

	var trans_type =  $("input[name='section_item_type']:checked").val();

    var transfer_to_section = $('#select_to_section').val();

    postData={'trans_data':trans_data,'section_item_type':trans_type,'trans_to_section':transfer_to_section,'id_branch':branch};

    $.ajax({

        type:'POST',

        url : base_url + 'index.php/admin_ret_section_transfer/ret_section_transfer/save',		

	 	dataType : 'json',		

	 	data : postData,

        success : function(data)

        {

            if(data.status)

            {

                    $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});

            }

            else

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});

            }

            window.location.reload();

        }

    });

    $(".overlay").css("display", "none");		

}







$('input[type=radio][name="section_item_type"]').change(function() {

    // alert(2);

    if(this.value == 1){

        $('.old_tagged').css("display","block");	

        $('.tagged').css("display","block");	

        $('.estimation').css("display","block");	

        $('.product').css("display","block");	

        $('.section_non_tagged').css("display","none");	

        $('.section').css("display","none");	

        $('.section_non_tagged').css("display","none");	

        

    }

    else if(this.value == 2){



        // $('#bt_nt_search_list').css("display","none");		

        $('.old_tagged').css("display","none");		

        $('.tagged').css("display","none");

        $('.estimation').css("display","none");

        $('.product').css("display","none");	

        $('.section').css("display","block");

        $('.section_non_tagged').css("display","block");				

    }



});

function getNonTaggedItem(){ 

	my_Date = new Date();

	// var prodId = ($("#nt_product").val() != ""?$("#id_product").val():'');

	$.ajax({

		 url:base_url+ "index.php/admin_ret_brntransfer/branch_transfer/getNonTaggedItem?nocache=" + my_Date.getUTCSeconds(),

         data: {'lot_dt_rng':"",'prodId':$('#prod_select').val(),'lotno':"",'from_brn':$("#branch_select").val()},  

		 dataType:"JSON",

		 type:"POST",

		 cache:false,

		 success:function(data){  

		 	 $("div.overlay").css("display", "none"); 

			 $('#nt_total').text(data.length);

			 var oTable = $('#bt_nt_search_list').DataTable();

			 oTable.clear().draw();

			 if (data!= null && data.length > 0)

			 {  	

				oTable = $('#bt_nt_search_list').dataTable({

						"bDestroy": true,

						"bInfo": true,

						"bFilter": true,

						"bSort": true,

						"order": [[ 0, "desc" ]],

						"dom": 'lBfrtip',

						"aaData"  : data,

						"aoColumns": [	{ "mDataProp":function ( row, type, val, meta ){ 

											return '<input type="checkbox" name="nt_item_sel[]" class="nt_item_sel" value="'+row.nt_item_sel+'"><input type="hidden" class="id_lot_inward_detail" name="id_lot_inward_detail[]" value="'+row.id_lot_inward_detail+'"><input type="hidden" class="id_nontag_item" name="id_nontag_item[]" value="'+row.id_nontag_item+'">';

										}}, 

										{ "mDataProp":function ( row, type, val, meta ){

											var cls = (row.lot_no == '' ? 'text-maroon' : '' );

											return '<input type="hidden" class="id_section" name="id_section[]" value="'+row.id_section+'"><span class="'+cls+'">'+row.section_name+'</span>';

										}},  

										{ "mDataProp":function ( row, type, val, meta ){

											var cls = (row.lot_no == '' ? 'text-maroon' : '' );

											return '<input type="hidden" class="product" name="product[]" value="'+row.product+'"><span class="'+cls+'">'+row.product_name+'</span>';

										}},

										{ "mDataProp":function ( row, type, val, meta ){

											var cls = (row.lot_no == '' ? 'text-maroon' : '' );

											return '<input type="hidden" class="design" name="design[]" value="'+row.design+'"><span class="'+cls+'">'+row.design_name+'</span>';

										}},  

										{ "mDataProp":function ( row, type, val, meta ){

											var cls = (row.lot_no == '' ? 'text-maroon' : '' );

											return '<input type="hidden" class="id_sub_design" name="id_sub_design[]" value="'+row.id_sub_design+'"><span class="'+cls+'">'+row.sub_design_name+'</span>';

										}},  

										{ "mDataProp":function ( row, type, val, meta ){

											var cls = (row.lot_no == '' ? 'text-maroon' : '' );

											return '<span class="'+cls+'"><input type="number" name="nt_piece[]" class="nt_piece col-md-6" value="'+row.no_of_piece+'" style="width: 100px;"><input type="hidden" class="blc_pieces col-md-6" value="'+row.no_of_piece+'"> of &nbsp;'+ row.no_of_piece +'<br/><span style="font-size:10px;color:red;" class="err"></span></span>';

										}},

										{ "mDataProp":function ( row, type, val, meta ){

											var cls = (row.lot_no == '' ? 'text-maroon' : '' );

											return '<span class="'+cls+'"><input type="number" step=any name="nt_gross_wt[]" class="nt_gross_wt col-md-6" style="width: 100px;" value="'+row.gross_wt+'"><input type="hidden" class="blc_gross_wt col-md-6" value="'+row.gross_wt+'"> of &nbsp;'+ row.gross_wt +'<br/><span style="font-size:10px;color:red;" class="err"></span></span>';

										}},

										{ "mDataProp":function ( row, type, val, meta ){

											var cls = (row.lot_no == '' ? 'text-maroon' : '' );

											return '<span class="'+cls+'"><input type="number" step=any name="nt_net_wgt[]" class="nt_net_wgt col-md-6" style="width: 100px;" value="'+row.net_wt+'"><input type="hidden" class="blc_net_wgt col-md-6" value="'+row.net_wt+'"> of &nbsp;'+ row.net_wt +'<br/><span style="font-size:10px;color:red;" class="err"></span></span>';

										}}   

									]

					});			  	 	

				}  

		  },

		  error:function(error)  

		  {

			 $("div.overlay").css("display", "none"); 

		  }	 

	});

}







$('#nt_select_all').click(function(event) {

    $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));

    event.stopPropagation();  

    calculateNTtotal();

});



$(document).on('click',".nt_item_sel", function(){

    calculateNTtotal();

});	

 

$(document).on('change',".id_lot_inward_detail", function(){

    calculateNTtotal();

});







$(document).on('input change',".nt_piece,.nt_gross_wt,.nt_net_wgt", function(){



    $("#bt_nt_search_list tbody tr").each(function (index, value){



        var row = $(this).closest('tr'); 

        

        pieces=parseFloat(row.find('.nt_piece').val());

        grs_wt=parseFloat(row.find('.nt_gross_wt').val());

        net_wt=parseFloat(row.find('.nt_net_wgt').val());



       nt_pieces= parseFloat(row.find('.blc_pieces').val()); 

       nt_grs_wt=parseFloat(row.find('.blc_gross_wt ').val());

       nt_net_wt=parseFloat(row.find('.blc_net_wgt ').val());

       console.log(pieces == nt_pieces);

    if(row.find("input[name='nt_item_sel[]']:checked").is(":checked"))  // whether checkbox is selected

    {

    if(pieces > nt_pieces)

    {   

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Pcs..'});

        $(".overlay").css("display", "none");

        row.find('.nt_piece').val(nt_pieces);

      

    }

    else if(grs_wt > nt_grs_wt)

    {

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Gross Weight..'});

        $(".overlay").css("display", "none");

        row.find('.nt_gross_wt').val(nt_grs_wt);

    }

    else if(net_wt > nt_net_wt)

    {

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Net Weight..'});

        $(".overlay").css("display", "none");

        row.find('.nt_net_wgt').val(nt_net_wt);

    }

    else if(parseFloat(grs_wt) < parseFloat(net_wt))

    {

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Net Weight Is Grater Than The Gross Weight..'});

        $(".overlay").css("display", "none");

    }

      }

    });

    

    calculateNTtotal();

});



function calculateNTtotal(){

    var pieces = 0;

    var grs_wt = 0;

    var net_wt = 0;

    $("#bt_nt_search_list input[type=checkbox]:checked").each(function () { 

        var row = $(this).closest('tr'); 

        pieces = pieces + (isNaN(row.find('.nt_piece').val() ) ? 0 : parseFloat(row.find('.nt_piece').val())); 

        grs_wt = grs_wt + (isNaN( row.find('.nt_gross_wt').val() ) ? 0 : parseFloat(row.find(' .nt_gross_wt').val()));

        net_wt = net_wt + (isNaN( row.find('.nt_net_wgt').val() ) ? 0 :parseFloat(row.find('.nt_net_wgt').val())) ;             

    });  

    $(".nt_pieces").val(pieces);

    $(".nt_grs_wt").val(grs_wt);

    $(".nt_net_wt").val(net_wt);

}







$('#send_counter_change_otp_yes').on('click',function()

{

		  $('.cancel_otp').css('display','block');

		  $('.cancel_otp_confirmation').css('display','none');

		  $('.verify_otp').css('display','block');

          var trans_type = $("input[name='section_item_type']:checked").val();

          var SectionTagData = [];



          if(trans_type==1){



            var tot_grs_wt = 0;

            var tot_pcs = 0;



            $("#section_trans_list tbody tr").each(function (index, value) {

                var row = $(this).closest('tr');

                if (row.find("input[name='tag_id[]']:checked").is(":checked"))  // whether checkbox is selected

                {



                    tot_grs_wt += parseFloat(row.find('.gross_wt').val());

                    tot_pcs += parseFloat(row.find('.piece').val());



                }



            });





          }

			counterchange_otp(tot_grs_wt,tot_pcs)

});





$(document).on('click', '#send_counter_change_otp_no',function()

{

    $("#section_transfer").prop('disabled', false);



    $('#confirm-sec_transotp').modal('toggle');

});



function counterchange_otp(gross,pcs)

{

	$("div.overlay").css("display","block");

	my_Date=new Date();

	$.ajax({

		url: base_url+'index.php/admin_ret_section_transfer/send_counterchange_otp/?nocache='+my_Date.getUTCSeconds(),             

        dataType: "json", 

        method: "POST",

		data:{'total_gwt': gross, 'tot_pcs': pcs , 'id_branch': $('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : $("#branch_select").val(),'from_section':$('#select_frm_section').val(),'to_section' :$('#select_to_section').val()}, 

		success: function (data) {

			console.log(data.status);

                if(data.status == true)

				{

				    $("div.overlay").css("display", "none"); 

                    $('#otp_by_emp').val('');

                	$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+data.msg});	

                	

                	var fewSeconds = 60;  

    		   		$("#resend_cancel_otp").prop('disabled', true);

    		   		timer = setTimeout(function(){

    			        $("#resend_cancel_otp").prop('disabled', false); 

    		    	}, fewSeconds*1000);

		    	

			      

				}

				else{

				    $("div.overlay").css("display", "none"); 

					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br> Unable to Send The OTP..."});

				}

                },

                error:function(error)  

                {

                    $("div.overlay").css("display", "none"); 

                }

	});

}





$('#verfiy_counter_change_otp').on('click',function(){

    my_Date = new Date();

	$.ajax({

        url: base_url+'index.php/admin_ret_section_transfer/verify_counter_change_otp/?nocache='+my_Date.getUTCSeconds(),

        data:{'otp':$('#sectrans_otp').val()},

        dataType: "json",

        method: "POST",

        success: function (data) {

                if(data.status == true)

				{

				    $('#confirm-sec_transotp').modal('toggle');

                    if (SectionTagData.length > 0) {



                        add_to_trans(SectionTagData)



                    }

 

				   allow_discount_otp = false

                   $.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+data.msg});

				}else{

				    $('#cc_disc_otp').val('');

				    $.toaster({ priority : 'danger', title : 'Success!', message : ''+"</br>"+data.msg});

				}

                },

                error:function(error)

                {

                }

    });

});



$('#counterchange_close_modal').on('click', function () {

	$('#confirm-sec_transotp').modal('toggle');

});

