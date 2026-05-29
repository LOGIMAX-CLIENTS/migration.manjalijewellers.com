var path =  url_params();

var ctrl_page = path.route.split('/');

$(document).ready(function() {



	$(' #giftJoin_modal').on('shown.bs.modal',function(){
    
			$("#acc_name").on('keypress', function (event) {
			    var theEvent = event || window.event;
				 var tab= theEvent.keyCode || theEvent.which;
				 if (tab === 9 ) { //TAB was pressed
					return true;
				 }
				 
				 
				  var regex = new RegExp("^[a-zA-Z _ \r\s]+$");
				  var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				  if (!regex.test(key)) {
					 event.preventDefault();
					 alert('Account name must contain alphabets only')
				  }
			   });
	});
    
});