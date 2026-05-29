$(document).ready(function() {


	$( "#coin_enq_submit" ).click(function( event ) {
		event.preventDefault();
		var proceed = validateForm();
		if(proceed){
			$('#coin_enq_form').submit();	
		}else{
			$("#err").html("Please Fill required fields")
		}
	});	
	
	
	
	
	
});

function validateForm(){
	var proceed = true;
	
	if(($("#name").val()).length == 0 ){
		$("#nameErr").html('Name Required'); 
		proceed = false;
	}
	if(($("#mobile").val()).length == 0 ){
		$("#mobErr").html('Mobile Required'); 
		proceed = false;
	}
	
	/* if($.trim($("#gram").val()) == '') 
                 
     { 
        alert('please select the coin');

        return false;
      }
      
      
      if($.trim($("#coin_type").val()) == '') 
                 
     { 
        alert('please select the coin type');

        return false;
      }*/

	if(($("#qty").val()).length == 0 ){
		$("#qtyErr").html('Quantity Required'); 
		proceed = false;
	}
	if(($("#comments").val()).length == 0 ){
		$("#msgErr").html('comments Required'); 
		proceed = false;
	}
	return proceed;
}


