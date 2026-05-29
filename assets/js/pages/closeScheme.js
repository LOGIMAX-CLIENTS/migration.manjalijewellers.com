$(document).ready(function() {
	closeScheme.changeScheme();
	$('#id_scheme_account').change(function(event) {
		event.preventDefault();
		closeScheme.changeScheme();
	});
	$('#IDSubmit').click(function(event) {
		event.preventDefault();
		closeScheme.confirmClose();
	});
	$('#confirm').click(function(event) {
		$("#close_scheme").submit();
	});
});	
closeScheme = {	
		changeScheme : function() {
			var selected_scheme = $("#id_scheme_account").val();
			$.each(objClose, function(index, value) {
				if(value.id_scheme_account == selected_scheme)
				{
					$("#scheme_acc_number").val(value.scheme_acc_number == '' ? 'Not Allocated' : value.scheme_acc_number);
					if(value.req_close == 1)
					{
						$(".closeAlert").css('display','none');	
						$("#commentsDiv").empty();
						$("#commentsDiv").css('display','none');
						$("#reqClose").html('<button type="submit" id="IDSubmit" name="IDSubmit" value="cancel" class="btn btn-info">Cancel Request</button>');
					}
					else
					{
						if(parseFloat(value.total_installments) > parseFloat(value.paid_installments))
						{
							if(value.paid_installments == 0)
								var customerMsg	= '<p>You have not done any payment.';
							else
								var customerMsg	= '<p>You have completed only <strong>'+value.paid_installments+'</strong> installment(s).';
								
							customerMsg = customerMsg + 'To avail full benefits of the scheme, you have to complete <strong>'+value.total_installments+'</strong> installments.';
							customerMsg = customerMsg + 'If you still want to close the scheme, please provide the reason for it.</p>'
							$(".closeAlert").removeClass('alert-success').addClass('alert-danger');
						}
						else
						{
							customerMsg = '<p>You have successfully completed '+value.total_installments+' months scheme. You can now avail full benefits of the scheme. Please click the <a href="'+baseURL+'index.php/dashboard">dashboard</a> to view your scheme summary.</p>';
							$(".closeAlert").removeClass('alert-danger').addClass('alert-success');
						}
						$(".closeAlertMsg").empty().append(customerMsg);
						$(".closeAlert").css('display','block');	
						$("#commentsDiv").css('display','block');
						$("#commentsDiv").empty().append('<label>Comments</label><textarea required id="remark_close" name="remark_close"></textarea>');
						$("#reqClose").html('<button type="submit" id="IDSubmit" name="IDSubmit" value="send"  class="btn btn-info">Send Request</button>');
					}
				}
			});
			},
			confirmClose : function() {
				if($("#remark_close").length > 0)
				{
					if($.trim($("#remark_close").val()) == '')
					{
						alert('Please fill the reason for closing the scheme');
						$("#remark_close").focus();
					}
					else
					{
						$("#schemeclose_modal .modal-body p").html("Are you sure ! you want to close the scheme");
						$('#schemeclose_modal').modal('show');
					}
						
				}
				else
				{
					$("#schemeclose_modal .modal-body p").html("Are you sure ! you want to cancel the request");
					$('#schemeclose_modal').modal('show');
				}
			}
}