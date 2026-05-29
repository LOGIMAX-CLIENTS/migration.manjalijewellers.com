$(document).ready(function(){
console.log(cus_ref_code);
if(cus_ref_code > 0){
	syncOfflineData();
}

my_Date=new Date();$.ajax({type:"GET",url:baseURL+"api/rate.txt"+"?nocache="+my_Date.getUTCSeconds(),dataType:"json",cache:false,success:function(data){$("#gold22ct").html(data.goldrate_22ct);}});});$(document).on('click',"a.btn-del",function(e){e.preventDefault();var link=$(this).data('href');$('#confirm-delete').find('.btn-confirm').attr('href',link);});$('#confirm-delete .btn-cancel').on('click',function(e){$('.btn-confirm').attr('href',"#");});$('.input_currency').on("keypress keyup blur",function(event){if((event.which!=46||$(this).val().indexOf('.')!=-1)&&((event.which<48||event.which>57)&&(event.which!=0&&event.which!=8))){event.preventDefault();}

var text=$(this).val();if((text.indexOf('.')!=-1)&&(text.substring(text.indexOf('.')).length>2)&&(event.which!=0&&event.which!=8)&&($(this)[0].selectionStart>=text.length-2)){event.preventDefault();}});$('.input_weight').on("keypress keyup blur",function(event){if((event.which!=46||$(this).val().indexOf('.')!=-1)&&((event.which<48||event.which>57)&&(event.which!=0&&event.which!=8))){event.preventDefault();}

var text=$(this).val();if((text.indexOf('.')!=-1)&&(text.substring(text.indexOf('.')).length>3)&&(event.which!=0&&event.which!=8)&&($(this)[0].selectionStart>=text.length-3)){event.preventDefault();}});$(".input_number").on("keypress keyup blur",function(event){var key=window.event?event.keyCode:event.which;if(event.keyCode==8||event.keyCode==46||event.keyCode==37||event.keyCode==39){return true;}

else if(key<48||key>57){return false;}

else return true;});$(".input_text").on("keypress keyup blur",function(event){var inputValue=event.charCode;if((inputValue>47&&inputValue<58)&&(inputValue!=32)){event.preventDefault();}});function minmax(value,min,max)

{if(parseInt(value)<min||isNaN(value)||value.length<=0)

return min;else if(parseInt(value)>max)

return max;else return value;}

function syncOfflineData(){
	my_Date = new Date();
	$.ajax({
		type: "POST",
		url: baseURL+"index.php/user/getDataFromOffline?nocache=" + my_Date.getUTCSeconds(),
		dataType: "json",
		cache: false,
		success: function(data) {
			console.log("Data Synced");
		}
	});//end of ajaxcall
}