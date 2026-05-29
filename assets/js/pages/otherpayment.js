$(document).ready(function() {
	$('#date_payment').datepicker({
    	format: 'dd-mm-yyyy',
    	startDate: '01/01/2000',
		endDate:"0d" 
	});
	$("#bank_name").focus();
});	